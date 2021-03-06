<?php

namespace App\Utils;

use Exception;
use \Firebase\JWT\JWT;

final class Utils {
	
	public static function getReverseJsonArray($url) {
		return array_reverse(Utils::getJsonArray($url), true);
	}

	public static function getJsonArray($url) {
		$curlSession = curl_init();
	    curl_setopt($curlSession, CURLOPT_URL, $url);
	    curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
	    curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

	    $jsonData = json_decode(curl_exec($curlSession), TRUE);
	    curl_close($curlSession);

		return (array) $jsonData;
	}

	public static function formatSizeUnits($bytes) {
		$original = $bytes;
		$bytes = intval(str_replace(",", "", $bytes));

		if(!ctype_digit($bytes))
			return $original;

		if ($bytes >= 1073741824) {
			$bytes = number_format($bytes / 1073741824, 2) . ' GB';
		} elseif ($bytes >= 1048576) {
			$bytes = number_format($bytes / 1048576, 2) . ' MB';
		} elseif ($bytes >= 1024) {
			$bytes = number_format($bytes / 1024, 2) . ' KB';
		} elseif ($bytes > 1) {
			$bytes = $bytes . ' bytes';
		} elseif ($bytes == 1) {
			$bytes = $bytes . ' byte';
		} else {
			$bytes = '0 bytes';
		}

		return $bytes;
	}

	public static function isUserAdmin($uid, $token) {
        $admins = Repo::getAdmins($uid, $token);

        if(isset($admins['error'])) {
            return $admins['error'];
        }

        return @$admins[0];
    }

    public static function getUserFromToken($token) {
        // TODO: Cache response and add appropriate cache expire time
        $key = Repo::getRobotsMetaData();

        JWT::$leeway = 60;
        return @JWT::decode($token, $key, ['RS256']);
    }

    public static function verifyKey($key, &$data) {
        if(!isset($key) || $key == '') {
            $data['message'] = 'No authorization header provided';
            return FALSE;
        }

        $matches = array();
        preg_match('/Bearer\s(\S+)/', $key, $matches);

        if(!isset($matches[1])){
            $data['message'] = 'No bearer in Authorization Header';
            return FALSE;
        }

        $token = $matches[1];

        try {
            $user = Utils::getUserFromToken($token);

            $isAdmin = Utils::isUserAdmin($user->user_id, $token);

            if($isAdmin != 'true') {
                $data['message'] = 'You do not have admin privileges!';
                return FALSE;
            }

        } catch (Exception $e) {
            $data['message'] = $e->getMessage();
            return FALSE;
        }

        return TRUE;
    }
}