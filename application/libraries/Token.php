<?php
require FCPATH . 'vendor/autoload.php';
use Firebase\JWT\JWT;

/**
 * AUTH
 */
const AUTH_JWT_SECRET = "METCHELITenTiVErodsAbiNCHwARIERFaEsITHuSeVNdvLNuTrPtltlPgtnMnld";

JWT::$leeway = 36000000;

class Token
{

    private $token;
    private $decoded;

    private $requestId;
    private $logger;

    public function __construct($param)
    {
        if ($param['flag'] == 1) {
            $this->retrieveToken();
            $this->validateToken();
        }
    }

    public function retrieveToken()
    {
        // This method will exist if you're using apache
        // If you're not, please go to the extras for a definition of it.
        $requestHeaders = apache_request_headers();
        if (isset($requestHeaders['Authorization'])) {
            $authorizationHeader = $requestHeaders['Authorization'] ?: $requestHeaders['authorization'];
            $this->token = str_replace('Bearer ', '', $authorizationHeader);
        } else {
            $this->unauthorizedError('Authorization Header Not Found');
        }
    }

    public function validateToken()
    {
        try {
            $this->decoded = JWT::decode($this->token, AUTH_JWT_SECRET, array('HS256'));
        } catch (\Exception $e) {
            $message = 'Unauthorized';
            $this->unauthorizedError($message);
        }
    }

    public function unauthorizedError($message)
    {
        header('HTTP/1.0 401 Unauthorized');
        $response['status_code'] = 401;
        $response['response_code'] = 1;
        $response['response_message'] = $message;
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit();
    }

    public function forbiddenError()
    {
        header('HTTP/1.0 403 Forbidden');
        $msg = "No access level for user.";

        $response['status'] = 1;
        $response['msg'] = $msg;
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit();
    }

    public function getUserID()
    {
        if ($this->decoded->userID == null) {
            $this->forbiddenError();
        } else {
            $CI = &get_instance();
            $secret = $this->decoded->token;
            $user_id = $this->decoded->userID;

            $jwt_token = $CI->db->select('user_id')->from('tbl_jwt_access_token')->where(array('secret' => $secret, 'user_id' => $user_id, 'revoked' => 0))->get()->row_array();
            if (empty($jwt_token)) {
                $this->forbiddenError();
            } else {
                $user = $CI->db->select('id, emp_code, firstname, middlename, lastname, email, address, city, state, zipcode, mobile_no, bloodgroup, gender, role_id, joining_as, experience_month, joindate, training_enddate, bond_startdate, bond_enddate, machine_id, basic_salary, bankname, bankaddress, bankbranch, bankacnum, bankifsc, bankactype')->from('tbl_user')->where(array('id' => $user_id))->get()->row_array();
                return $user;
            }
        }
    }

    public function logout()
    {
        if ($this->decoded->userID == null) {
            $this->forbiddenError();
        } else {
            $CI = &get_instance();
            $secret = $this->decoded->token;
            $user_id = $this->decoded->userID;

            $CI->db->select('revoked');
            $CI->db->from('tbl_jwt_access_token');
            $CI->db->where(array('secret' => $secret, 'user_id' => $user_id, 'revoked' => 0));
            $jwt_token = $CI->db->get()->row();

            if (empty($jwt_token)) {
                $this->forbiddenError();
            } else {
                $update_data = array('revoked' => 1);
                $CI->db->where($user_id);
                $CI->db->update('tbl_jwt_access_token', $update_data);
                if ($CI->db->affected_rows() > 0) {
                    return true;
                } else {
                    return false;
                }
            }
            return false;
        }
    }

    public static function getAuthUserToken($userID)
    {
        $CI = &get_instance();
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 15; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $token = array(
            "iss" => "Demo_jwt",
            "origin" => "php",
            "userID" => $userID,
            "token" => $randomString,
        );

        $data = array(
            'user_id' => $userID,
            'secret' => $randomString,
            'revoked' => 0,
        );

        $CI->db->insert('tbl_jwt_access_token', $data);
        if ($CI->db->affected_rows() > 0) {
            $jwt = JWT::encode($token, AUTH_JWT_SECRET);
            return $jwt;
        } else {
            return false;
        }
    }
}
