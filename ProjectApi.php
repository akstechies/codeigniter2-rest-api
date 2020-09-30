<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

// Load the Rest Controller library
require APPPATH . 'libraries/Rest.php';

class ProjectApi extends Rest
{

    public function __construct()
    {
        parent::__construct();

        $this->load->database();
    }
    public function get_category()
    {
        $categories = $this->db->get('mst_category')->result_array();
        if (!empty($categories)) {

            foreach ($categories as $key => $val) {
                $cat['category_name'] = $val['category_name'];
                $cat['image'] = $val['image'];
                $cate[$key] = $cat;
            }
            $this->response([
                'Status' => TRUE,
                'Message' => 'Data Availaible.',
                'Data' => $cate
            ], 200);
        } else {
            $this->response([
                'Status' => FALSE,
                'Message' => 'No Data Found.',
                'Data' => $categories
            ], 200);
        }
    }

    public function get_offers()
    {
        $categories = $this->db->get('tbl_coupons')->result_array();
        if (!empty($categories)) {

            foreach ($categories as $key => $val) {
                $cat['coupon_name'] = $val['name'];
                $cat['image'] = $val['image'];
                $cate[$key] = $cat;
            }
            $this->response([
                'Status' => TRUE,
                'Message' => 'Data Availaible.',
                'Data' => $cate
            ], 200);
        } else {
            $this->response([
                'Status' => FALSE,
                'Message' => 'No Data Found.',
                'Data' => $categories
            ], 200);
        }
    }

    public function post_deliveryBoyOtp()
    {
        $mobile = strip_tags($this->post('mobile'));
        $query = $this->db->get_where('tbl_deliveryboy', array('mobile' => $this->post('mobile')));
        $row = $query->num_rows();
        if ($row > 0) {
            $data = array();
            $data1 = array();
            $ran = mt_rand('1000', '3000');
            $otp = "$ran";
            $data['status'] = "TRUE";
            $data['OTP'] = $otp;
            $query = $this->db->get_where('tbl_deliveryboy', array('mobile' => $this->post('mobile')));
            $data = $query->row_array();
            $this->response(['Status' => 'TRUE', 'Message' => 'Mobile Number Already exist', 'OTP' => $otp, 'Mobile' => $mobile, 'Data' => $data], 200);
            
            $mess = "Your OTP is $otp. Please do not share your OTP with anyone.";
            $url  = "";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $curl_scraped_page = curl_exec($ch);
            curl_close($ch);
        } else {
            $query = $this->db->get_where('tbl_deliveryboy', array('mobile' => $this->post('mobile')));
            $data = $query->row_array();
            $val = $data;
            $ran = mt_rand('1000', '3000');
            $otp = "$ran";
            $data['status'] = "TRUE";
            $data['OTP'] = $otp;
            $this->response(['Status' => 'TRUE', 'Message' => 'Mobile not Exist', 'OTP' => $otp, 'Mobile' => $mobile, 'Data' => $val], 200);
            
            $mess = "Your OTP is $otp. Please do not share your OTP with anyone.";
            $url  = "";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $curl_scraped_page = curl_exec($ch);
            curl_close($ch);
        }
    }

    public function post_addDeliveryBoyInfo()
    {
        $field['fullname'] = $this->post('fullname');
        $field['mobile'] = $this->post('mobile');
        $field['email'] = $this->post('email');
        $field['address'] = $this->post('address');
        $password = $this->post('password');
        $field['password'] = md5($password . $this->SecretHash());
        $field['pancardno'] = $this->post('pancardno');
        $field['drivinglicencenumber'] = $this->post('drivinglicencenumber');
        $field['vehicletype'] = $this->post('vehicletype');
        $field['vehiclenumber'] = $this->post('vehiclenumber');
        $field['insurancepolicyexpirydate'] = $this->post('insurancepolicyexpirydate');
        $field['licenceexpiry'] = $this->post('licenceexpiry');
        $field['upiid'] = $this->post('upiid');
        $field['pollutionexpiry'] = $this->post('pollutionexpiry');
        $field['rcexpiry'] = $this->post('rcexpiry');

        $deliveryboyrow = $this->db->get('tbl_deliveryboy')->num_rows();
        $deliveryboy_num_row = $deliveryboyrow + 1;
        $field['dboy_id'] = "DBOY00" . $deliveryboy_num_row;

        date_default_timezone_set('Asia/Kolkata');
        $field['createddate'] = date('Y-m-d H:i');

        if (!empty($field)) {
            $this->db->insert('tbl_deliveryboy', $field);
            $this->response([
                'Status' => "TRUE",
                'Message' => 'Delivery Boy Has been Added',
                'Data' => [$field]
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'Some Problems Occured! Please Try Again'
            ], 200);
        }
    }

    public function post_addDBoyBankInfo()
    {
        $field['dboy_id'] = $this->post('dboy_id');
        $field['ifsccode'] = $this->post('ifsccode');
        $field['bankname'] = $this->post('bankname');
        $field['accountholder'] = $this->post('accountholder');
        $field['accountnumber'] = $this->post('accountnumber');

        $query = $this->db->get_where('tbl_deliveryboy', array('dboy_id' => $field['dboy_id']));
        $row = $query->num_rows();

        if ($row > 0) {
            $this->db->where('dboy_id', $field['dboy_id']);
            $this->db->update('tbl_deliveryboy', $field);
            $this->response([
                'Status' => "TRUE",
                'Message' => 'Delivery Boy Bank Details Has been Added',
                'Data' => $field
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'Some Problems Occured! Please Try Again'
            ], 200);
        }
    }

    public function get_products()
    {
        $categories = $this->db->get('tbl_products')->result_array();
        if (!empty($categories)) {

            foreach ($categories as $key => $val) {
                $cat['id'] = $val['id'];
                $cat['product_name'] = $val['product_name'];
                $cat['price'] = $val['price'];

                $this->db->where('product_id', $val['id']);
                $vendor = $this->db->get('tbl_productdetails')->row_array();
                $cat['image'] = $vendor['image'];

                $cate[$key] = $cat;
            }
            $this->response([
                'Status' => TRUE,
                'Message' => 'Data Availaible.',
                'Data' => $cate
            ], 200);
        } else {
            $this->response([
                'Status' => FALSE,
                'Message' => 'No Data Found.',
                'Data' => $categories
            ], 200);
        }
    }

    function post_uploadDBoyProfile()
    {
        $dboy_id   = $this->input->post('dboy_id');
        $base64       = $this->input->post('profile_pic');

        $data = $this->db->get_where('tbl_deliveryboy', array('dboy_id' => $dboy_id))->row_array();
        if (!empty($data)) {
            $ImageName = "DBoyProfile_" . time();
            $PROFILE_DIRECTORY = './uploads/deliveryboy/';
            $img = @imagecreatefromstring(base64_decode($base64));
            if ($img != false) {
                $imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
                $path = $PROFILE_DIRECTORY . $imageName;
                $data = array('profile_pic' => $imageName);
                $this->db->where('dboy_id', $dboy_id);
                $this->db->update('tbl_deliveryboy', $data);
                $this->response([
                    "Status" => "TRUE",
                    "Message" => "Delivery Boy Profile Pic Uploaded successfully"
                ], 200);
                if (imagejpeg($img, $path)) {
                    return $imageName;
                    $this->response([
                        "Status" => "FALSE",
                        "Message" => "Delivery Boy Profile Pic Uploaded successfully"
                    ], 200);
                } else {
                    $Message = array('Message' => 'Data INSERTION FAILED');
                    echo json_encode($Message);
                }
            }
        } else {
            $Message = array('Message' => 'Data NOT EXISTS');
            echo json_encode($Message);
        }
    }

    public function get_banner()
    {
        $categories = $this->db->get('tbl_banner')->result_array();
        if (!empty($categories)) {

            foreach ($categories as $key => $val) {
                $cat['image'] = $val['image'];
                $cate[$key] = $cat;
            }
            $this->response([
                'Status' => TRUE,
                'Message' => 'Data Availaible.',
                'Data' => $cate
            ], 200);
        } else {
            $this->response([
                'Status' => FALSE,
                'Message' => 'No Data Found.',
                'Data' => $categories
            ], 200);
        }
    }

    function post_uploadDBoyLicense()
    {
        $dboy_id   = $this->input->post('dboy_id');
        $base64       = $this->input->post('licenceimage');

        $data = $this->db->get_where('tbl_deliveryboy', array('dboy_id' => $dboy_id))->row_array();
        if (!empty($data)) {
            $ImageName = "DBoyLicense_" . time();
            $PROFILE_DIRECTORY = './uploads/deliveryboy/';
            $img = @imagecreatefromstring(base64_decode($base64));
            if ($img != false) {
                $imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
                $path = $PROFILE_DIRECTORY . $imageName;
                $data = array('licenceimage' => $imageName);
                $this->db->where('dboy_id', $dboy_id);
                $this->db->update('tbl_deliveryboy', $data);
                $this->response([
                    "Status" => "TRUE",
                    "Message" => "Delivery Boy License Uploaded successfully"
                ], 200);
                if (imagejpeg($img, $path)) {
                    return $imageName;
                    $this->response([
                        "Status" => "FALSE",
                        "Message" => "Delivery Boy License Uploaded successfully"
                    ], 200);
                } else {
                    $Message = array('Message' => 'Data INSERTION FAILED');
                    echo json_encode($Message);
                }
            }
        } else {
            $Message = array('Message' => 'Data NOT EXISTS');
            echo json_encode($Message);
        }
    }

    function post_uploadDBoyInsurance()
    {
        $dboy_id   = $this->input->post('dboy_id');
        $base64       = $this->input->post('policyverificationimage');

        $data = $this->db->get_where('tbl_deliveryboy', array('dboy_id' => $dboy_id))->row_array();
        if (!empty($data)) {
            $ImageName = "DBoyInsurance_" . time();
            $PROFILE_DIRECTORY = './uploads/deliveryboy/';
            $img = @imagecreatefromstring(base64_decode($base64));
            if ($img != false) {
                $imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
                $path = $PROFILE_DIRECTORY . $imageName;
                $data = array('policyverificationimage' => $imageName);
                $this->db->where('dboy_id', $dboy_id);
                $this->db->update('tbl_deliveryboy', $data);
                $this->response([
                    "Status" => "TRUE",
                    "Message" => "Delivery Boy Insurance Uploaded successfully"
                ], 200);
                if (imagejpeg($img, $path)) {
                    return $imageName;
                    $this->response([
                        "Status" => "FALSE",
                        "Message" => "Delivery Boy Insurance Uploaded successfully"
                    ], 200);
                } else {
                    $Message = array('Message' => 'Data INSERTION FAILED');
                    echo json_encode($Message);
                }
            }
        } else {
            $Message = array('Message' => 'Data NOT EXISTS');
            echo json_encode($Message);
        }
    }

    function post_uploadDBoyMou()
    {
        $dboy_id   = $this->input->post('dboy_id');
        $base64       = $this->input->post('mouimage');

        $data = $this->db->get_where('tbl_deliveryboy', array('dboy_id' => $dboy_id))->row_array();
        if (!empty($data)) {
            $ImageName = "DBoyMou_" . time();
            $PROFILE_DIRECTORY = './uploads/deliveryboy/';
            $img = @imagecreatefromstring(base64_decode($base64));
            if ($img != false) {
                $imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
                $path = $PROFILE_DIRECTORY . $imageName;
                $data = array('mouimage' => $imageName);
                $this->db->where('dboy_id', $dboy_id);
                $this->db->update('tbl_deliveryboy', $data);
                $this->response([
                    "Status" => "TRUE",
                    "Message" => "Delivery Boy MOU Uploaded successfully"
                ], 200);
                if (imagejpeg($img, $path)) {
                    return $imageName;
                    $this->response([
                        "Status" => "FALSE",
                        "Message" => "Delivery Boy MOU Uploaded successfully"
                    ], 200);
                } else {
                    $Message = array('Message' => 'Data INSERTION FAILED');
                    echo json_encode($Message);
                }
            }
        } else {
            $Message = array('Message' => 'Data NOT EXISTS');
            echo json_encode($Message);
        }
    }

    function post_uploadDBoyAddress()
    {
        $dboy_id   = $this->input->post('dboy_id');
        $base64       = $this->input->post('addressproofimage');

        $data = $this->db->get_where('tbl_deliveryboy', array('dboy_id' => $dboy_id))->row_array();
        if (!empty($data)) {
            $ImageName = "DBoyAdd_" . time();
            $PROFILE_DIRECTORY = './uploads/deliveryboy/';
            $img = @imagecreatefromstring(base64_decode($base64));
            if ($img != false) {
                $imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
                $path = $PROFILE_DIRECTORY . $imageName;
                $data = array('addressproofimage' => $imageName);
                $this->db->where('dboy_id', $dboy_id);
                $this->db->update('tbl_deliveryboy', $data);
                $this->response([
                    "Status" => "TRUE",
                    "Message" => "Delivery Boy Address Proof Uploaded successfully"
                ], 200);
                if (imagejpeg($img, $path)) {
                    return $imageName;
                    $this->response([
                        "Status" => "FALSE",
                        "Message" => "Delivery Boy Address Proof Uploaded successfully"
                    ], 200);
                } else {
                    $Message = array('Message' => 'Data INSERTION FAILED');
                    echo json_encode($Message);
                }
            }
        } else {
            $Message = array('Message' => 'Data NOT EXISTS');
            echo json_encode($Message);
        }
    }

    function post_uploadDBoyPanCard()
    {
        $dboy_id   = $this->input->post('dboy_id');
        $base64       = $this->input->post('pancardimage');

        $data = $this->db->get_where('tbl_deliveryboy', array('dboy_id' => $dboy_id))->row_array();
        if (!empty($data)) {
            $ImageName = "DBoyPan_" . time();
            $PROFILE_DIRECTORY = './uploads/deliveryboy/';
            $img = @imagecreatefromstring(base64_decode($base64));
            if ($img != false) {
                $imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
                $path = $PROFILE_DIRECTORY . $imageName;
                $data = array('pancardimage' => $imageName);
                $this->db->where('dboy_id', $dboy_id);
                $this->db->update('tbl_deliveryboy', $data);
                $this->response([
                    "Status" => "TRUE",
                    "Message" => "Delivery Boy pancardimage Uploaded successfully"
                ], 200);
                if (imagejpeg($img, $path)) {
                    return $imageName;
                    $this->response([
                        "Status" => "FALSE",
                        "Message" => "Delivery Boy pancardimage Uploaded successfully"
                    ], 200);
                } else {
                    $Message = array('Message' => 'Data INSERTION FAILED');
                    echo json_encode($Message);
                }
            }
        } else {
            $Message = array('Message' => 'Data NOT EXISTS');
            echo json_encode($Message);
        }
    }

    function post_uploadDBoyCheque()
    {
        $dboy_id   = $this->input->post('dboy_id');
        $base64       = $this->input->post('cancelchequeimage');

        $data = $this->db->get_where('tbl_deliveryboy', array('dboy_id' => $dboy_id))->row_array();
        if (!empty($data)) {
            $ImageName = "DBoyCheque_" . time();
            $PROFILE_DIRECTORY = './uploads/deliveryboy/';
            $img = @imagecreatefromstring(base64_decode($base64));
            if ($img != false) {
                $imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
                $path = $PROFILE_DIRECTORY . $imageName;
                $data = array('cancelchequeimage' => $imageName);
                $this->db->where('dboy_id', $dboy_id);
                $this->db->update('tbl_deliveryboy', $data);
                $this->response([
                    "Status" => "TRUE",
                    "Message" => "Delivery Boy cancelchequeimage Uploaded successfully"
                ], 200);
                if (imagejpeg($img, $path)) {
                    return $imageName;
                    $this->response([
                        "Status" => "FALSE",
                        "Message" => "Delivery Boy cancelchequeimage Uploaded successfully"
                    ], 200);
                } else {
                    $Message = array('Message' => 'Data INSERTION FAILED');
                    echo json_encode($Message);
                }
            }
        } else {
            $Message = array('Message' => 'Data NOT EXISTS');
            echo json_encode($Message);
        }
    }

    function post_uploadDBoypollution()
    {
        $dboy_id   = $this->input->post('dboy_id');
        $base64       = $this->input->post('pollutionimage');

        $data = $this->db->get_where('tbl_deliveryboy', array('dboy_id' => $dboy_id))->row_array();
        if (!empty($data)) {
            $ImageName = "DBoyCheque_" . time();
            $PROFILE_DIRECTORY = './uploads/deliveryboy/';
            $img = @imagecreatefromstring(base64_decode($base64));
            if ($img != false) {
                $imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
                $path = $PROFILE_DIRECTORY . $imageName;
                $data = array('pollutionimage' => $imageName);
                $this->db->where('dboy_id', $dboy_id);
                $this->db->update('tbl_deliveryboy', $data);
                $this->response([
                    "Status" => "TRUE",
                    "Message" => "Delivery Boy pollutionimage Uploaded successfully"
                ], 200);
                if (imagejpeg($img, $path)) {
                    return $imageName;
                    $this->response([
                        "Status" => "FALSE",
                        "Message" => "Delivery Boy pollutionimage Uploaded successfully"
                    ], 200);
                } else {
                    $Message = array('Message' => 'Data INSERTION FAILED');
                    echo json_encode($Message);
                }
            }
        } else {
            $Message = array('Message' => 'Data NOT EXISTS');
            echo json_encode($Message);
        }
    }

    function post_uploadDBoyRC()
    {
        $dboy_id   = $this->input->post('dboy_id');
        $base64       = $this->input->post('rcimage');

        $data = $this->db->get_where('tbl_deliveryboy', array('dboy_id' => $dboy_id))->row_array();
        if (!empty($data)) {
            $ImageName = "DBoyCheque_" . time();
            $PROFILE_DIRECTORY = './uploads/deliveryboy/';
            $img = @imagecreatefromstring(base64_decode($base64));
            if ($img != false) {
                $imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
                $path = $PROFILE_DIRECTORY . $imageName;
                $data = array('rcimage' => $imageName);
                $this->db->where('dboy_id', $dboy_id);
                $this->db->update('tbl_deliveryboy', $data);
                $this->response([
                    "Status" => "TRUE",
                    "Message" => "Delivery Boy rcimage Uploaded successfully"
                ], 200);
                if (imagejpeg($img, $path)) {
                    return $imageName;
                    $this->response([
                        "Status" => "FALSE",
                        "Message" => "Delivery Boy rcimage Uploaded successfully"
                    ], 200);
                } else {
                    $Message = array('Message' => 'Data INSERTION FAILED');
                    echo json_encode($Message);
                }
            }
        } else {
            $Message = array('Message' => 'Data NOT EXISTS');
            echo json_encode($Message);
        }
    }

    public function get_vendor()
    {
        $categories = $this->db->get('vendor')->result_array();
        if (!empty($categories)) {

            foreach ($categories as $key => $val) {
                $cat['companyname'] = $val['companyname'];
                $cat['cityid'] = $val['cityid'];
                $cat['profile_pic'] = $val['profile_pic'];

                $this->db->where('id', $val['cityid']);
                $city = $this->db->get('mst_city')->row_array();
                $cat['city'] = $city['name'];

                $cate[$key] = $cat;
            }
            $this->response([
                'Status' => TRUE,
                'Message' => 'Data Availaible.',
                'Data' => $cate
            ], 200);
        } else {
            $this->response([
                'Status' => FALSE,
                'Message' => 'No Data Found.',
                'Data' => $categories
            ], 200);
        }
    }

    public function SecretHash()
    {
        return 'MY*S3C537#4$H';
    }

    public function post_dBoyLoginOtp()
    {
        $mobile = strip_tags($this->post('mobile'));
        $pass = strip_tags($this->post('password'));

        $password = md5($pass . $this->SecretHash());

        $query = $this->db->get_where('tbl_deliveryboy', array('mobile' => $mobile, 'password' => $password));
        $row = $query->num_rows();
        if ($row > 0) {
            $data = array();
            $data1 = array();
            $ran = mt_rand('1000', '3000');
            $otp = "$ran";
            $data['status'] = "TRUE";
            $data['OTP'] = $otp;
            $query = $this->db->get_where('tbl_deliveryboy', array('mobile' => $this->post('mobile')));
            $data = $query->row_array();
            $this->response(['Status' => 'TRUE', 'Message' => 'Data Exist', 'OTP' => $otp, 'dboy_id' => $data['dboy_id'], 'Mobile' => $mobile, 'Data' => [$data]], 200);
            
            $mess = "Your OTP is $otp. Please do not share your OTP with anyone.";
            $url  = "";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $curl_scraped_page = curl_exec($ch);
            curl_close($ch);
        } else {
            $query = $this->db->get_where('tbl_deliveryboy', array('mobile' => $this->post('mobile')));
            $data = $query->row_array();
            $val = $data;
            $ran = mt_rand('1000', '3000');
            $otp = "$ran";
            $data['status'] = "TRUE";
            $data['OTP'] = $otp;
            $this->response(['Status' => 'TRUE', 'Message' => 'Data not Exist or Password Incorrect', 'OTP' => $otp, 'Mobile' => $mobile, 'Data' => $val], 200);
            
            $mess = "Your OTP is $otp. Please do not share your OTP with anyone.";
            $url  = "";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $curl_scraped_page = curl_exec($ch);
            curl_close($ch);
        }
    }

    public function post_getDBoyById()
    {
        $dboy_id = strip_tags($this->post('dboy_id'));
        $this->db->where('dboy_id', $dboy_id);
        $query = $this->db->get('tbl_deliveryboy');
        $row = $query->row_array();
        if (!$row) {
            $data = array();
            $data1 = array();
            $data1['Status'] = "False";
            $data1['Message'] = "No Data Found";
            $data = $data1;
            $this->response($data, REST::HTTP_OK);
        } else {
            $this->db->where('dboy_id', $dboy_id);
            $query = $this->db->get('tbl_deliveryboy');
            $data = $query->row_array();

            $partner['dboy_id'] = $data['dboy_id'];
            $partner['fullname'] = $data['fullname'];
            $partner['email'] = $data['email'];
            $partner['vehiclenumber'] = $data['vehiclenumber'];
            $partner['vehicletype'] = $data['vehicletype'];
            $partner['profile_pic'] = $data['profile_pic'];
            $partner['mobile'] = $data['mobile'];

            $this->response([
                "Status" => "TRUE",
                "Message" => "Data Found",
                "Data" => [$partner]
            ], REST::HTTP_OK);
        }
    }

    public function post_editDBoyProfile()
    {
        $field['dboy_id'] = $this->post('dboy_id');
        $field['fullname'] = $this->post('fullname');
        $field['mobile'] = $this->post('mobile');
        $field['email'] = $this->post('email');


        $query = $this->db->get_where('tbl_deliveryboy', array('dboy_id' => $field['dboy_id']));
        $row = $query->num_rows();

        if ($row > 0) {
            $this->db->where('dboy_id', $field['dboy_id']);
            $this->db->update('tbl_deliveryboy', $field);
            $this->response([
                'Status' => "TRUE",
                'Message' => 'Delivery Boy Bank Details Has been Added',
                'Data' => [$field]
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'Some Problems Occured! Please Try Again'
            ], 200);
        }
    }

    function post_updateDBoyProfile()
    {
        $dboy_id   = $this->input->post('dboy_id');
        $fullname   = $this->input->post('fullname');
        $mobile   = $this->input->post('mobile');
        $email  = $this->input->post('email');
        $base64       = $this->input->post('profile_pic');

        $data = $this->db->get_where('tbl_deliveryboy', array('dboy_id' => $dboy_id))->row_array();
        if (!empty($data)) {
            $ImageName = "DBoyProfile_" . time();
            $PROFILE_DIRECTORY = './uploads/deliveryboy/';
            $img = @imagecreatefromstring(base64_decode($base64));
            if ($img != false) {
                $imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
                $path = $PROFILE_DIRECTORY . $imageName;
                $data = array('profile_pic' => $imageName, 'fullname' => $fullname, 'mobile' => $mobile, 'email' => email);
                $this->db->where('dboy_id', $dboy_id);
                $this->db->update('tbl_deliveryboy', $data);
                $this->response([
                    "Status" => "TRUE",
                    "Message" => "Delivery Boy Profile Updated successfully"
                ], 200);
                if (imagejpeg($img, $path)) {
                    return $imageName;
                    $this->response([
                        "Status" => "FALSE",
                        "Message" => "Delivery Boy Profile Updated successfully"
                    ], 200);
                } else {
                    $Message = array('Message' => 'Data INSERTION FAILED');
                    echo json_encode($Message);
                }
            }
        } else {
            $Message = array('Message' => 'Data NOT EXISTS');
            echo json_encode($Message);
        }
    }

    public function get_orderCount()
    {

        $this->db->where('status', 1);
        $data['new'] = $this->db->count_all_results('tbl_vendor_order');

        $this->db->where('status', 2);
        $data['onway'] = $this->db->count_all_results('tbl_vendor_order');

        $this->db->where('status', 3);
        $data['delivered'] = $this->db->count_all_results('tbl_vendor_order');

        $this->db->where('status', 4);
        $data['cancelled'] = $this->db->count_all_results('tbl_vendor_order');

        $this->db->where('status', 8);
        $data['accepted'] = $this->db->count_all_results('tbl_vendor_order');

        $this->db->where('status', 8);
        $data['accepted'] = $this->db->count_all_results('tbl_vendor_order');

        $this->db->where('status', 9);
        $data['returned'] = $this->db->count_all_results('tbl_vendor_order');

        if (!empty($data)) {

            $this->response([
                'Status' => "TRUE",
                'Message' => 'Showing Order Count',
                'Data' => [$data]
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'Some Error Occured.',
                'Data' => [$data]
            ], 200);
        }
    }

    public function post_getDBoyEarning()
    {
        $dboy_id = strip_tags($this->post('dboy_id'));
        $this->db->where('dboy_id', $dboy_id);
        $query = $this->db->get('tbl_deliveryboybonus');
        $row = $query->row_array();
        if (!$row) {
            $data = array();
            $data1 = array();
            $data1['Status'] = "False";
            $data1['Message'] = "No Data Found";
            $data = $data1;
            $this->response($data, REST::HTTP_OK);
        } else {
            $this->db->where('dboy_id', $dboy_id);
            $query = $this->db->get('tbl_deliveryboybonus');
            $data = $query->result_array();

            foreach ($data as $key => $val) {
                $partner['id'] = $val['id'];
                $partner['dboy_id'] = $val['dboy_id'];
                $partner['price'] = $val['price'];
                $partner['created_at'] = $val['created_at'];

                $dboy[$key] = $partner;
            }


            $this->response([
                "Status" => "TRUE",
                "Message" => "Data Found",
                "Data" => $dboy
            ], REST::HTTP_OK);
        }
    }

    public function get_newOrderList()
    {
        $categories = $this->db->get('tbl_order')->result_array();
        if (!empty($categories)) {

            foreach ($categories as $key => $val) {
                $cat['orderid'] = $val['orderid'];
                $cat['first_name'] = $val['first_name'];
                $cat['last_name'] = $val['last_name'];
                $cat['order_number'] = $val['order_number'];
                $cat['order_date'] = $val['order_date'];
                $cat['total_amount'] = $val['total_amount'];
                $cat['total_price'] = $val['total_price'];
                $cate[$key] = $cat;
            }
            $this->response([
                'Status' => TRUE,
                'Message' => 'Data Availaible.',
                'Data' => $cate
            ], 200);
        } else {
            $this->response([
                'Status' => FALSE,
                'Message' => 'No Data Found.'
            ], 200);
        }
    }

    public function post_vendorOrderList()
    {

        $vendor_id = $this->post('vendor_id');

        $this->db->where('vendor_id', $vendor_id);
        $categories = $this->db->get('tbl_vendor_order')->result_array();
        if (!empty($categories)) {

            foreach ($categories as $key => $val) {
                $cat['id'] = $val['id'];
                $cat['vendor_id'] = $val['vendor_id'];
                $cat['order_id'] = $val['order_id'];
                $cat['cart_id'] = $val['cart_id'];
                $cat['statusid'] = $val['status'];

                $this->db->where('id', $cat['vendor_id']);
                $vendor = $this->db->get('vendor')->row_array();
                // $cat['lat'] = $vendor['lat'];

                $this->db->where('orderid', $cat['order_id']);
                $order = $this->db->get('tbl_order')->row_array();
                $cat['customername'] = $order['first_name'] . " " . $order['last_name'];
                $cat['address1'] = $order['address1'];
                $cat['orderdetails'] = $order['orderdetails'];
                $cat['userid'] = $order['userid'];
                $cat['order_date'] = $order['order_date'];
                $cat['mobilenumer'] = $order['mobilenumer'];
                $cat['comission'] = $order['comission'];
                $cat['quantity'] = $order['quantity'];
                $cat['total_price'] = $order['total_price'];



                $this->db->where('statusid', $val['status']);
                $order = $this->db->get('order_status')->row_array();
                $cat['order_status'] = $order['name'];


                $cate[$key] = $cat;
            }
            $this->response([
                'Status' => "TRUE",
                'Message' => 'Data Availaible.',
                'Data' => $cate
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'No Data Found.',
                'Data' => $categories
            ], 200);
        }
    }

    public function get_vendorOnWayOrder()
    {
        $this->db->where('status', 2);
        $categories = $this->db->get('tbl_vendor_order')->result_array();
        if (!empty($categories)) {

            foreach ($categories as $key => $val) {
                $cat['id'] = $val['id'];
                $cat['vendor_id'] = $val['vendor_id'];
                $cat['order_id'] = $val['order_id'];
                $cat['cart_id'] = $val['cart_id'];

                $this->db->where('id', $cat['vendor_id']);
                $vendor = $this->db->get('vendor')->row_array();
                $cat['lat'] = $vendor['lat'];
                $cat['lng'] = $vendor['lng'];

                $this->db->where('orderid', $cat['order_id']);
                $order = $this->db->get('tbl_order')->row_array();
                $cat['name'] = $order['first_name'] . " " . $order['last_name'];
                $cat['order_date'] = $order['order_date'];
                $cat['total_amount'] = $order['total_amount'];
                $cat['total_price'] = $order['total_price'];



                $cate[$key] = $cat;
            }
            $this->response([
                'Status' => "TRUE",
                'Message' => 'Data Availaible.',
                'Data' => $cate
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'No Data Found.',
                'Data' => $categories
            ], 200);
        }
    }

    public function get_vendorDeliveredOrder()
    {
        $this->db->where('status', 3);
        $categories = $this->db->get('tbl_vendor_order')->result_array();
        if (!empty($categories)) {

            foreach ($categories as $key => $val) {
                $cat['id'] = $val['id'];
                $cat['vendor_id'] = $val['vendor_id'];
                $cat['order_id'] = $val['order_id'];
                $cat['cart_id'] = $val['cart_id'];

                $this->db->where('id', $cat['vendor_id']);
                $vendor = $this->db->get('vendor')->row_array();
                $cat['lat'] = $vendor['lat'];
                $cat['lng'] = $vendor['lng'];

                $this->db->where('orderid', $cat['order_id']);
                $order = $this->db->get('tbl_order')->row_array();
                $cat['name'] = $order['first_name'] . " " . $order['last_name'];
                $cat['order_date'] = $order['order_date'];
                $cat['total_amount'] = $order['total_amount'];
                $cat['total_price'] = $order['total_price'];



                $cate[$key] = $cat;
            }
            $this->response([
                'Status' => "TRUE",
                'Message' => 'Data Availaible.',
                'Data' => $cate
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'No Data Found.',
                'Data' => $categories
            ], 200);
        }
    }

    public function get_vendorCancelOrder()
    {
        $this->db->where('status', 4);
        $categories = $this->db->get('tbl_vendor_order')->result_array();
        if (!empty($categories)) {

            foreach ($categories as $key => $val) {
                $cat['id'] = $val['id'];
                $cat['vendor_id'] = $val['vendor_id'];
                $cat['order_id'] = $val['order_id'];
                $cat['cart_id'] = $val['cart_id'];

                $this->db->where('id', $cat['vendor_id']);
                $vendor = $this->db->get('vendor')->row_array();
                $cat['lat'] = $vendor['lat'];
                $cat['lng'] = $vendor['lng'];

                $this->db->where('orderid', $cat['order_id']);
                $order = $this->db->get('tbl_order')->row_array();
                $cat['name'] = $order['first_name'] . " " . $order['last_name'];
                $cat['order_date'] = $order['order_date'];
                $cat['total_amount'] = $order['total_amount'];
                $cat['total_price'] = $order['total_price'];



                $cate[$key] = $cat;
            }
            $this->response([
                'Status' => "TRUE",
                'Message' => 'Data Availaible.',
                'Data' => $cate
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'No Data Found.',
                'Data' => $categories
            ], 200);
        }
    }

    public function get_vendorAcceptedOrder()
    {
        $this->db->where('status', 8);
        $categories = $this->db->get('tbl_vendor_order')->result_array();
        if (!empty($categories)) {

            foreach ($categories as $key => $val) {
                $cat['id'] = $val['id'];
                $cat['vendor_id'] = $val['vendor_id'];
                $cat['order_id'] = $val['order_id'];
                $cat['cart_id'] = $val['cart_id'];

                $this->db->where('id', $cat['vendor_id']);
                $vendor = $this->db->get('vendor')->row_array();
                $cat['lat'] = $vendor['lat'];
                $cat['lng'] = $vendor['lng'];

                $this->db->where('orderid', $cat['order_id']);
                $order = $this->db->get('tbl_order')->row_array();
                $cat['name'] = $order['first_name'] . " " . $order['last_name'];
                $cat['order_date'] = $order['order_date'];
                $cat['total_amount'] = $order['total_amount'];
                $cat['total_price'] = $order['total_price'];



                $cate[$key] = $cat;
            }
            $this->response([
                'Status' => "TRUE",
                'Message' => 'Data Availaible.',
                'Data' => $cate
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'No Data Found.',
                'Data' => $categories
            ], 200);
        }
    }

    public function post_acceptedOrder()
    {
        $order_id = $this->post('order_id');
        $this->db->where('orderid', $order_id);
        $val = $this->db->get('tbl_order')->row_array();

        if (!empty($val)) {

            $cat['orderid'] = $val['orderid'];
            $cat['order_number'] = $val['order_number'];
            $cat['order_date'] = $val['order_date'];
            $cat['name'] = $val['first_name'] . " " . $val['last_name'];
            $cat['mobilenumber'] = $val['mobilenumer'];
            $cat['cartid'] = $val['cartid'];
            $cat['total_amount'] = $val['total_amount'];
            $cat['total_price'] = $val['total_price'];
            $cat['orderdetails'] = $val['orderdetails'];
            $cat['address'] = $val['address1'];
            $cat['city'] = $val['city'];

            $cat['vendor_id'] = $val['vendor_id'];

            $cat['statusid'] = $val['status'];
            $this->db->where('id', $val['status']);
            $stats = $this->db->get('booking_status')->row_array();
            $cat['status'] = $stats['status'];

            $this->db->where('id', $val['vendor_id']);
            $vendor = $this->db->get('vendor')->row_array();

            $cat['fullname'] = $vendor['fullname'];
            $cat['companyname'] = $vendor['companyname'];
            $cat['mobile'] = $vendor['mobile'];
            $cat['vendor_address'] = "Gomtinagar Lucknow";
            $cat['lat'] = $vendor['lat'];
            $cat['lng'] = $vendor['lng'];

            $cat['cartvalue'] = 3;



            $this->response([
                'Status' => "TRUE",
                'Message' => 'Data Availaible.',
                'Data' => [$cat]
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'No Data Found.'
            ], 200);
        }
    }

    public function post_userLoginOtp()
    {
        $mobile = strip_tags($this->post('mobile'));
        $query = $this->db->get_where('customer', array('mobile' => $this->post('mobile')));
        $row = $query->num_rows();
        if ($row > 0) {
            $data = array();
            $data1 = array();
            $ran = mt_rand('1000', '3000');
            $otp = "1000";
            $data['status'] = "TRUE";
            $data['OTP'] = $otp;
            $query = $this->db->get_where('customer', array('mobile' => $this->post('mobile')));
            $data = $query->row_array();
            $this->response(['Status' => 'TRUE', 'Message' => 'Mobile Number Already exist', 'OTP' => $otp, 'Mobile' => $mobile, 'Data' => $data], 200);
            
            $mess = "Your OTP is $otp. Please do not share your OTP with anyone.";
            $url  = "";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $curl_scraped_page = curl_exec($ch);
            curl_close($ch);
        } else {
            $query = $this->db->get_where('customer', array('mobile' => $this->post('mobile')));
            $data = $query->row_array();
            $val = $data;
            $ran = mt_rand('1000', '3000');
            $otp = "1000";
            $data['status'] = "TRUE";
            $data['OTP'] = $otp;
            $this->response(['Status' => 'TRUE', 'Message' => 'Mobile not Exist', 'OTP' => $otp, 'Mobile' => $mobile, 'Data' => $val], 200);
            
            $mess = "Your OTP is $otp. Please do not share your OTP with anyone.";
            $url  = "";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $curl_scraped_page = curl_exec($ch);
            curl_close($ch);
        }
    }

    public function post_searchProduct()
    {
        $product = strip_tags($this->post('product_name'));

        if (empty($product)) {
            $this->response([
                'Status' => FALSE,
                'Message' => 'Fields are empty'
            ], REST_Controller::HTTP_OK);
        } else {


            $this->db->where('product_name', $product);


            $data = $this->db->get_where('tbl_products')->result_array();

            if (!empty($data)) {
                $this->response([
                    'Status' => "TRUE",
                    'Message' => 'Data Availaible.',
                    'Data' =>  $data
                ], REST::HTTP_OK);
            } else {
                $this->response([
                    'Status' => "FALSE",
                    'Message' => 'Nothing was found.',
                    'Data' => $data
                ], REST::HTTP_NOT_FOUND);
            }
        }
    }

    public function post_userDeliveryLoc()
    {
        $id = $this->post('cust_id');
        $field['latitude'] = $this->post('latitude');
        $field['logitude'] = $this->post('logitude');
        $field['location'] = $this->post('location');
        $field['flat_no'] = $this->post('flat_no');
        $field['landmark'] = $this->post('landmark');
        $field['save_as'] = $this->post('save_as');

        $query = $this->db->get_where('customer', array('id' => $id));
        $row = $query->num_rows();

        if ($row > 0) {
            $this->db->where('id', $id);
            $this->db->update('customer', $field);
            $this->response([
                'Status' => "TRUE",
                'Message' => 'Customer Location Has been Added',
                'Data' => $field
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'Some Problems Occured! Please Try Again'
            ], 200);
        }
    }

    public function post_deliveredOrder()
    {
        $order_id = $this->post('order_id');
        $this->db->where('orderid', $order_id);
        $val = $this->db->get('tbl_order')->row_array();

        if (!empty($val)) {

            $cat['orderid'] = $val['orderid'];
            $cat['order_number'] = $val['order_number'];
            $cat['order_date'] = $val['order_date'];
            $cat['name'] = $val['first_name'] . " " . $val['last_name'];
            $cat['mobilenumber'] = $val['mobilenumer'];
            $cat['cartid'] = $val['cartid'];
            $cat['total_amount'] = $val['total_amount'];
            $cat['total_price'] = $val['total_price'];
            $cat['orderdetails'] = $val['orderdetails'];
            $cat['address'] = $val['address1'];
            $cat['city'] = $val['city'];

            $cat['vendor_id'] = $val['vendor_id'];

            $cat['statusid'] = $val['status'];
            $this->db->where('id', $val['status']);
            $stats = $this->db->get('booking_status')->row_array();
            $cat['status'] = $stats['status'];

            $this->db->where('id', $val['vendor_id']);
            $vendor = $this->db->get('vendor')->row_array();

            $cat['fullname'] = $vendor['fullname'];
            $cat['companyname'] = $vendor['companyname'];
            $cat['mobile'] = $vendor['mobile'];
            $cat['vendor_address'] = "Gomtinagar Lucknow";
            $cat['lat'] = $vendor['lat'];
            $cat['lng'] = $vendor['lng'];

            $cat['cartvalue'] = 3;



            $this->response([
                'Status' => "TRUE",
                'Message' => 'Data Availaible.',
                'Data' => [$cat]
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'No Data Found.'
            ], 200);
        }
    }

    public function post_newOrder()
    {
        $order_id = $this->post('order_id');
        $this->db->where('orderid', $order_id);
        $val = $this->db->get('tbl_order')->row_array();

        if (!empty($val)) {

            $cat['orderid'] = $val['orderid'];
            $cat['order_number'] = $val['order_number'];
            $cat['order_date'] = $val['order_date'];
            $cat['name'] = $val['first_name'] . " " . $val['last_name'];
            $cat['mobilenumber'] = $val['mobilenumer'];
            $cat['cartid'] = $val['cartid'];
            $cat['total_amount'] = $val['total_amount'];
            $cat['total_price'] = $val['total_price'];
            $cat['orderdetails'] = $val['orderdetails'];
            $cat['address'] = $val['address1'];
            $cat['city'] = $val['city'];


            $cat['vendor_id'] = $val['vendor_id'];

            $cat['statusid'] = $val['status'];
            $this->db->where('id', $val['status']);
            $stats = $this->db->get('booking_status')->row_array();
            $cat['status'] = $stats['status'];

            $this->db->where('id', $val['vendor_id']);
            $vendor = $this->db->get('vendor')->row_array();

            $cat['fullname'] = $vendor['fullname'];
            $cat['companyname'] = $vendor['companyname'];
            $cat['mobile'] = $vendor['mobile'];
            $cat['vendor_address'] = "Gomtinagar Lucknow";
            $cat['lat'] = $vendor['lat'];
            $cat['lng'] = $vendor['lng'];

            $cat['cartvalue'] = 3;



            $this->response([
                'Status' => "TRUE",
                'Message' => 'Data Availaible.',
                'Data' => [$cat]
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'No Data Found.'
            ], 200);
        }
    }

    public function post_dBoyEarningDescDate()
    {
        $dboy_id = strip_tags($this->post('dboy_id'));
        $this->db->where('dboy_id', $dboy_id);
        $query = $this->db->get('tbl_deliveryboybonus');
        $row = $query->row_array();
        if (!$row) {
            $data = array();
            $data1 = array();
            $data1['Status'] = "False";
            $data1['Message'] = "No Data Found";
            $data = $data1;
            $this->response($data, REST::HTTP_OK);
        } else {
            $this->db->where('dboy_id', $dboy_id);
            $query = $this->db->query("SELECT * FROM tbl_deliveryboybonus WHERE `dboy_id`='DBOY004' ORDER BY created_at DESC");


            $data = $query->result_array();


            foreach ($data as $key => $val) {
                $partner['id'] = $val['id'];
                $partner['dboy_id'] = $val['dboy_id'];
                $partner['price'] = $val['price'];
                $partner['created_at'] = $val['created_at'];

                $dboy[$key] = $partner;
            }


            $this->response([
                "Status" => "TRUE",
                "Message" => "Data Found",
                "Data" => $dboy
            ], REST::HTTP_OK);
        }
    }

    public function post_dBoyEarningAscDate()
    {
        $dboy_id = strip_tags($this->post('dboy_id'));
        $this->db->where('dboy_id', $dboy_id);
        $query = $this->db->get('tbl_deliveryboybonus');
        $row = $query->row_array();
        if (!$row) {
            $data = array();
            $data1 = array();
            $data1['Status'] = "False";
            $data1['Message'] = "No Data Found";
            $data = $data1;
            $this->response($data, REST::HTTP_OK);
        } else {
            $this->db->where('dboy_id', $dboy_id);
            $query = $this->db->query("SELECT * FROM tbl_deliveryboybonus WHERE `dboy_id`='DBOY004' ORDER BY created_at ASC");


            $data = $query->result_array();


            foreach ($data as $key => $val) {
                $partner['id'] = $val['id'];
                $partner['dboy_id'] = $val['dboy_id'];
                $partner['price'] = $val['price'];
                $partner['created_at'] = $val['created_at'];

                $dboy[$key] = $partner;
            }


            $this->response([
                "Status" => "TRUE",
                "Message" => "Data Found",
                "Data" => $dboy
            ], REST::HTTP_OK);
        }
    }

    public function post_filterdBoyEarning()
    {

        $dboy_id = strip_tags($this->post('dboy_id'));


        $from_date = $this->post('from');
        $to_date = $this->post('to');

        $this->db->where('dboy_id', $dboy_id);
        $quer = $this->db->query("SELECT * FROM tbl_deliveryboybonus WHERE created_at BETWEEN '" . $from_date . "' AND  '" . $to_date . "' ORDER by id DESC");


        $data = $quer->result_array();

        if (!empty($data)) {

            foreach ($data as $key => $val) {
                $partner['id'] = $val['id'];
                $partner['dboy_id'] = $val['dboy_id'];
                $partner['price'] = $val['price'];
                $partner['created_at'] = $val['created_at'];

                $dboy[$key] = $partner;
            }

            $this->response([
                'Status' => "TRUE",
                'Message' => 'Data Availaible.',
                'Data' => $dboy
            ], REST::HTTP_OK);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'None was found.'
            ], REST::HTTP_OK);
        }
    }

    public function post_customerOtp()
    {
        $mobile = strip_tags($this->post('mobile'));
        $query = $this->db->get_where('customer', array('mobile' => $this->post('mobile')));
        $row = $query->num_rows();
        if ($row > 0) {
            $data = array();
            $data1 = array();
            $ran = mt_rand('1000', '3000');
            $otp = "1000";
            $data['status'] = "TRUE";
            $data['OTP'] = $otp;
            $query = $this->db->get_where('customer', array('mobile' => $this->post('mobile')));
            $data = $query->row_array();
            $this->response(['Status' => 'TRUE', 'Message' => 'Mobile Number Already exist', 'OTP' => $otp, 'Mobile' => $mobile, 'Data' => $data], 200);
            
            $mess = "Your OTP is $otp. Please do not share your OTP with anyone.";
            $url  = "";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $curl_scraped_page = curl_exec($ch);
            curl_close($ch);
        } else {
            $query = $this->db->get_where('customer', array('mobile' => $this->post('mobile')));
            $data = $query->row_array();
            $val = $data;
            $ran = mt_rand('1000', '3000');
            $otp = "1000";
            $data['status'] = "TRUE";
            $data['OTP'] = $otp;
            $this->response(['Status' => 'TRUE', 'Message' => 'Mobile not Exist', 'OTP' => $otp, 'Mobile' => $mobile, 'Data' => $val], 200);
            
            $mess = "Your OTP is $otp. Please do not share your OTP with anyone.";
            $url  = "";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $curl_scraped_page = curl_exec($ch);
            curl_close($ch);
        }
    }

    public function post_vendorOtp()
    {
        $mobile = strip_tags($this->post('mobile'));
        $query = $this->db->get_where('vendor', array('mobile' => $this->post('mobile')));
        $row = $query->num_rows();
        if ($row > 0) {
            $data = array();
            $data1 = array();
            $ran = mt_rand('1000', '3000');
            $otp = "$ran";
            $data['status'] = "TRUE";
            $data['OTP'] = $otp;
            $query = $this->db->get_where('vendor', array('mobile' => $this->post('mobile')));
            $data = $query->row_array();
            $this->response(['Status' => 'TRUE', 'Message' => 'Mobile Number Already exist', 'OTP' => $otp, 'Mobile' => $mobile, 'Data' => $data], 200);
            
            $mess = "Your OTP is $otp. Please do not share your OTP with anyone.";
            $url  = "";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $curl_scraped_page = curl_exec($ch);
            curl_close($ch);
        } else {
            $query = $this->db->get_where('tbl_deliveryboy', array('mobile' => $this->post('mobile')));
            $data = $query->row_array();
            $val = $data;
            $ran = mt_rand('1000', '3000');
            $otp = "$ran";
            $data['status'] = "TRUE";
            $data['OTP'] = $otp;
            $this->response(['Status' => 'TRUE', 'Message' => 'Mobile not Exist', 'OTP' => $otp, 'Mobile' => $mobile, 'Data' => $val], 200);
            
            $mess = "Your OTP is $otp. Please do not share your OTP with anyone.";
            $url  = "";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $curl_scraped_page = curl_exec($ch);
            curl_close($ch);
        }
    }

    public function post_addVendorInfo()
    {
        $field['categoryid'] = $this->post('categoryid');
        $field['fullname'] = $this->post('fullname');
        $field['companyname'] = $this->post('companyname');
        $field['mobile'] = $this->post('mobile');
        $field['email'] = $this->post('email');
        $field['address'] = $this->post('address');
        $password = $this->post('password');
        $field['password'] = md5($password . $this->SecretHash());
        $field['dob'] = $this->post('dob');
        $field['countryid'] = $this->post('countryid');
        $field['stateid'] = $this->post('stateid');
        $field['cityid'] = $this->post('cityid');

        $field['gstno'] = $this->post('gstno');
        $field['faxno'] = $this->post('faxno');
        $field['aadharcardno'] = $this->post('aadharcardno');
        $field['pincode'] = $this->post('pincode');
        $field['document1'] = $this->post('document1');
        $field['document2'] = $this->post('document2');




        if (!empty($field)) {
            $this->db->insert('vendor', $field);
            $this->response([
                'Status' => "TRUE",
                'Message' => 'Vendor Boy Has been Added',
                'Data' => [$field]
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'Some Problems Occured! Please Try Again'
            ], 200);
        }
    }

    public function get_vendorInfoCategory()
    {
        $data = $this->db->get('mst_category')->result_array();

        $i = 0;
        $add = array();
        if (!empty($data)) {
            foreach ($data as $value => $val) {
                $add[$i]['categoryid'] = $val['categoryid'];
                $add[$i]['category_name'] = $val['category_name'];
                $i++;
            }
            //print_r($add); 
            $this->response([
                'Status' => "TRUE",
                'Message' => 'Data Availaible.',
                'Data' => $add
            ], REST::HTTP_OK);
            //  }
            // Set the response and exit
            //OK (200) being the HTTP response code
        } else {
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'Status' => "FALSE",
                'Message' => 'None was found.'
            ], REST::HTTP_NOT_FOUND);
        }
    }

    public function get_vendorInfoCountry()
    {
        // Returns all the users data if the id not specified,
        // Otherwise, a single user will be returned.
        //$con = $id?array('id' => $id):'';
        $data = $this->db->get('mst_country')->result_array();

        // Check if the user data exists

        //echo "<pre>"; print_r($data); die;
        $i = '1';
        $add = array();
        if (!empty($data)) {
            $add[0]['countryid'] = null;
            $add[0]['country_name'] = 'Select Country';
            foreach ($data as $value => $val) {
                $add[$i]['countryid'] = $val['id'];
                $add[$i]['country_name'] = $val['name'];
                $i++;
            }
            //print_r($add); 
            $this->response([
                'Status' => "TRUE",
                'Message' => 'Data Availaible.',
                'Data' => $add
            ], REST::HTTP_OK);
            //  }
            // Set the response and exit
            //OK (200) being the HTTP response code
        } else {
            // Set the response and exit
            //NOT_FOUND (404) being the HTTP response code
            $this->response([
                'Status' => "FALSE",
                'Message' => 'None was found.'
            ], REST::HTTP_NOT_FOUND);
        }
    }

    public function post_vendorInfoState()
    {

        $country_id = $this->post('country_id');

        $this->db->where('country_id', $country_id);
        $data = $this->db->get('mst_state')->result_array();

        $i = '1';
        $add = array();
        if (!empty($data)) {
            $add[0]['stateid'] = null;
            $add[0]['state_name'] = 'Select State';
            foreach ($data as $value => $val) {
                $add[$i]['stateid'] = $val['id'];
                $add[$i]['state_name'] = $val['name'];
                $add[$i]['state_code'] = $val['state_code'];
                $add[$i]['country_code'] = $val['country_code'];
                $add[$i]['country_id'] = $val['country_id'];

                $i++;
            }
            //print_r($add); 
            $this->response([
                'Status' => "TRUE",
                'Message' => 'Data Availaible.',
                'Data' => $add
            ], REST::HTTP_OK);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'None was found.'
            ], REST::HTTP_NOT_FOUND);
        }
    }

    public function post_vendorInfoCity()
    {

        $state_id = $this->post('state_id');

        $this->db->where('state_id', $state_id);
        $data = $this->db->get('mst_city')->result_array();

        $i = '1';
        $add = array();
        if (!empty($data)) {
            $add[0]['cityid'] = null;
            $add[0]['city_name'] = 'Select City';
            foreach ($data as $value => $val) {
                $add[$i]['cityid'] = $val['id'];
                $add[$i]['city_name'] = $val['name'];
                $add[$i]['state_id'] = $val['state_id'];

                $i++;
            }
            //print_r($add); 
            $this->response([
                'Status' => "TRUE",
                'Message' => 'Data Availaible.',
                'Data' => $add
            ], REST::HTTP_OK);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'None was found.'
            ], REST::HTTP_NOT_FOUND);
        }
    }

    public function post_vendorOrderCount()
    {

        $vendor_id = $this->post('vendor_id');

        $this->db->where('status', 1);
        $this->db->where('vendor_id', $vendor_id);
        $data['new'] = $this->db->count_all_results('tbl_vendor_order');

        $this->db->where('status', 2);
        $this->db->where('vendor_id', $vendor_id);
        $data['onway'] = $this->db->count_all_results('tbl_vendor_order');

        $this->db->where('status', 3);
        $this->db->where('vendor_id', $vendor_id);
        $data['delivered'] = $this->db->count_all_results('tbl_vendor_order');

        $this->db->where('status', 4);
        $this->db->where('vendor_id', $vendor_id);
        $data['cancelled'] = $this->db->count_all_results('tbl_vendor_order');

        $this->db->where('status', 8);
        $this->db->where('vendor_id', $vendor_id);
        $data['accepted'] = $this->db->count_all_results('tbl_vendor_order');

        $this->db->where('status', 8);
        $this->db->where('vendor_id', $vendor_id);
        $data['accepted'] = $this->db->count_all_results('tbl_vendor_order');

        $this->db->where('status', 9);
        $this->db->where('vendor_id', $vendor_id);
        $data['returned'] = $this->db->count_all_results('tbl_vendor_order');

        if (!empty($data)) {

            $this->response([
                'Status' => "TRUE",
                'Message' => 'Showing Order Count',
                'Data' => [$data]
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'Some Error Occured.',
                'Data' => [$data]
            ], 200);
        }
    }

    public function post_acceptNewVendorOrder()
    {
        $id = $this->post('vendor_order_id');

        $field['status'] = $this->post('status');

        $query = $this->db->get_where('tbl_vendor_order', array('id' => $id));
        $row = $query->num_rows();

        if ($row > 0) {
            $this->db->where('id', $id);
            $this->db->update('tbl_vendor_order', $field);
            $val = $this->db->get_where('tbl_vendor_order', array('id' => $id))->row_array();

            $cat['id'] = $val['id'];
            $cat['vendor_id'] = $val['vendor_id'];
            $cat['order_id'] = $val['order_id'];
            $cat['cart_id'] = $val['cart_id'];
            $cat['statusid'] = $val['status'];

            $this->db->where('id', $cat['vendor_id']);
            $vendor = $this->db->get('vendor')->row_array();
            // $cat['lat'] = $vendor['lat'];

            $this->db->where('orderid', $cat['order_id']);
            $order = $this->db->get('tbl_order')->row_array();
            $cat['customername'] = $order['first_name'] . " " . $order['last_name'];
            $cat['address1'] = $order['address1'];
            $cat['orderdetails'] = $order['orderdetails'];
            $cat['userid'] = $order['userid'];
            $cat['order_date'] = $order['order_date'];
            $cat['mobilenumer'] = $order['mobilenumer'];
            $cat['comission'] = $order['comission'];
            $cat['quantity'] = $order['quantity'];
            $cat['total_price'] = $order['total_price'];
            $cat['product_name'] = $order['product_name'];




            $this->db->where('statusid', $val['status']);
            $order = $this->db->get('order_status')->row_array();
            $cat['order_status'] = $order['name'];



            $this->response([
                'Status' => "TRUE",
                'Message' => 'Success!',
                'Data' => $cat
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'Some Problems Occured! Please Try Again'
            ], 200);
        }
    }

    public function post_addVendorBankInfo()
    {
        $mobile = $this->post('mobile');
        $field['ifsccode'] = $this->post('ifsccode');
        $field['bankholdername'] = $this->post('bankholdername');
        $field['bankname'] = $this->post('bankname');
        $field['accountno'] = $this->post('accountno');

        $query = $this->db->get_where('vendor', array('mobile' => $mobile));
        $row = $query->num_rows();

        if ($row > 0) {
            $this->db->where('mobile', $mobile);
            $this->db->update('vendor', $field);
            $this->response([
                'Status' => "TRUE",
                'Message' => 'Vendor Bank Details Has been Added',
                'Data' => $field
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'Some Problems Occured! Please Try Again'
            ], 200);
        }
    }

    function post_uploadVendorProfile()
    {
        $mobile   = $this->input->post('mobile');
        $base64       = $this->input->post('profile_pic');

        $data = $this->db->get_where('vendor', array('mobile' => $mobile))->row_array();
        if (!empty($data)) {
            $ImageName = "VendorProfile_" . time();
            $PROFILE_DIRECTORY = './uploads/vendor/';
            $img = @imagecreatefromstring(base64_decode($base64));
            if ($img != false) {
                $imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
                $path = $PROFILE_DIRECTORY . $imageName;
                $data = array('profile_pic' => $imageName);
                $this->db->where('mobile', $mobile);
                $this->db->update('vendor', $data);
                $this->response([
                    "Status" => "TRUE",
                    "Message" => "Vendor Pic Uploaded successfully"
                ], 200);
                if (imagejpeg($img, $path)) {
                    return $imageName;
                    $this->response([
                        "Status" => "FALSE",
                        "Message" => "Delivery Boy Profile Pic Uploaded successfully"
                    ], 200);
                } else {
                    $Message = array('Message' => 'Data INSERTION FAILED');
                    echo json_encode($Message);
                }
            }
        } else {
            $Message = array('Message' => 'Data NOT EXISTS');
            echo json_encode($Message);
        }
    }

    function post_uploadVendorDocFirst()
    {
        $mobile   = $this->input->post('mobile');
        $base64       = $this->input->post('doc1file');

        $data = $this->db->get_where('vendor', array('mobile' => $mobile))->row_array();
        if (!empty($data)) {
            $ImageName = "VendorProfile_" . time();
            $PROFILE_DIRECTORY = './uploads/vendor/';
            $img = @imagecreatefromstring(base64_decode($base64));
            if ($img != false) {
                $imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
                $path = $PROFILE_DIRECTORY . $imageName;
                $data = array('doc1file' => $imageName);
                $this->db->where('mobile', $mobile);
                $this->db->update('vendor', $data);
                $this->response([
                    "Status" => "TRUE",
                    "Message" => "Vendor Pic Uploaded successfully"
                ], 200);
                if (imagejpeg($img, $path)) {
                    return $imageName;
                    $this->response([
                        "Status" => "FALSE",
                        "Message" => "Delivery Boy Profile Pic Uploaded successfully"
                    ], 200);
                } else {
                    $Message = array('Message' => 'Data INSERTION FAILED');
                    echo json_encode($Message);
                }
            }
        } else {
            $Message = array('Message' => 'Data NOT EXISTS');
            echo json_encode($Message);
        }
    }

    function post_uploadVendorDocSecond()
    {
        $mobile   = $this->input->post('mobile');
        $base64       = $this->input->post('doc2file');

        $data = $this->db->get_where('vendor', array('mobile' => $mobile))->row_array();
        if (!empty($data)) {
            $ImageName = "VendorProfile_" . time();
            $PROFILE_DIRECTORY = './uploads/vendor/';
            $img = @imagecreatefromstring(base64_decode($base64));
            if ($img != false) {
                $imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
                $path = $PROFILE_DIRECTORY . $imageName;
                $data = array('doc2file' => $imageName);
                $this->db->where('mobile', $mobile);
                $this->db->update('vendor', $data);
                $this->response([
                    "Status" => "TRUE",
                    "Message" => "Vendor Pic Uploaded successfully"
                ], 200);
                if (imagejpeg($img, $path)) {
                    return $imageName;
                    $this->response([
                        "Status" => "FALSE",
                        "Message" => "Delivery Boy Profile Pic Uploaded successfully"
                    ], 200);
                } else {
                    $Message = array('Message' => 'Data INSERTION FAILED');
                    echo json_encode($Message);
                }
            }
        } else {
            $Message = array('Message' => 'Data NOT EXISTS');
            echo json_encode($Message);
        }
    }

    function post_uploadVendorTnfile()
    {
        $mobile   = $this->input->post('mobile');
        $base64       = $this->input->post('tnfile');

        $data = $this->db->get_where('vendor', array('mobile' => $mobile))->row_array();
        if (!empty($data)) {
            $ImageName = "VendorProfile_" . time();
            $PROFILE_DIRECTORY = './uploads/vendor/';
            $img = @imagecreatefromstring(base64_decode($base64));
            if ($img != false) {
                $imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
                $path = $PROFILE_DIRECTORY . $imageName;
                $data = array('tnfile' => $imageName);
                $this->db->where('mobile', $mobile);
                $this->db->update('vendor', $data);
                $this->response([
                    "Status" => "TRUE",
                    "Message" => "Vendor Pic Uploaded successfully"
                ], 200);
                if (imagejpeg($img, $path)) {
                    return $imageName;
                    $this->response([
                        "Status" => "FALSE",
                        "Message" => "Delivery Boy Profile Pic Uploaded successfully"
                    ], 200);
                } else {
                    $Message = array('Message' => 'Data INSERTION FAILED');
                    echo json_encode($Message);
                }
            }
        } else {
            $Message = array('Message' => 'Data NOT EXISTS');
            echo json_encode($Message);
        }
    }

    function post_uploadVendorFssaifilee()
    {
        $mobile   = $this->input->post('mobile');
        $base64       = $this->input->post('fssaifile');

        $data = $this->db->get_where('vendor', array('mobile' => $mobile))->row_array();
        if (!empty($data)) {
            $ImageName = "VendorProfile_" . time();
            $PROFILE_DIRECTORY = './uploads/vendor/';
            $img = @imagecreatefromstring(base64_decode($base64));
            if ($img != false) {
                $imageName  = ($ImageName != '') ? $ImageName . '.png' : 'sign' . generate_unique_code1() . time() . '.png';
                $path = $PROFILE_DIRECTORY . $imageName;
                $data = array('fssaifile' => $imageName);
                $this->db->where('mobile', $mobile);
                $this->db->update('vendor', $data);
                $this->response([
                    "Status" => "TRUE",
                    "Message" => "Vendor Pic Uploaded successfully"
                ], 200);
                if (imagejpeg($img, $path)) {
                    return $imageName;
                    $this->response([
                        "Status" => "FALSE",
                        "Message" => "Delivery Boy Profile Pic Uploaded successfully"
                    ], 200);
                } else {
                    $Message = array('Message' => 'Data INSERTION FAILED');
                    echo json_encode($Message);
                }
            }
        } else {
            $Message = array('Message' => 'Data NOT EXISTS');
            echo json_encode($Message);
        }
    }

    public function post_vendorLoginOtp()
    {
        $mobile = strip_tags($this->post('mobile'));
        $pass = strip_tags($this->post('password'));

        $password = md5($pass . $this->SecretHash());

        $query = $this->db->get_where('vendor', array('mobile' => $mobile, 'password' => $password));
        $row = $query->num_rows();
        if ($row > 0) {
            $data = array();
            $data1 = array();
            $data['status'] = "TRUE";
            $query = $this->db->get_where('vendor', array('mobile' => $this->post('mobile')));
            $data = $query->row_array();
            $this->response(['Status' => 'TRUE', 'Message' => 'Data Exist', 'Mobile' => $mobile, 'Data' => [$data]], 200);
        } else {
            $query = $this->db->get_where('vendor', array('mobile' => $this->post('mobile')));
            $data = $query->row_array();
            $val = $data;
            $data['status'] = "TRUE";
            $this->response(['Status' => 'FALSE', 'Message' => 'Data not Exist or Password Incorrect', 'Mobile' => $mobile, 'Data' => $val], 200);
        }
    }

    public function post_getVendorEarning()
    {
        $dboy_id = strip_tags($this->post('vendor_id'));
        $this->db->where('testid', $dboy_id);
        $query = $this->db->get('tbl_deliveryboybonus');
        $row = $query->row_array();
        if (!$row) {
            $data = array();
            $data1 = array();
            $data1['Status'] = "False";
            $data1['Message'] = "No Data Found";
            $data = $data1;
            $this->response($data, REST::HTTP_OK);
        } else {
            $this->db->where('testid', $dboy_id);
            $query = $this->db->get('tbl_deliveryboybonus');
            $data = $query->result_array();

            foreach ($data as $key => $val) {
                $partner['id'] = $val['id'];
                $partner['price'] = $val['price'];
                $partner['created_at'] = $val['created_at'];

                $dboy[$key] = $partner;
            }


            $this->response([
                "Status" => "TRUE",
                "Message" => "Data Found",
                "Data" => $dboy
            ], REST::HTTP_OK);
        }
    }

    public function post_editVendorProfile()
    {
        $id = $this->post('vendor_id');
        $field['fullname'] = $this->post('fullname');
        $field['mobile'] = $this->post('mobile');
        $field['email'] = $this->post('email');


        $query = $this->db->get_where('vendor', array('id' => $id));
        $row = $query->num_rows();

        if ($row > 0) {
            $this->db->where('id', $id);
            $this->db->update('vendor', $field);
            $this->response([
                'Status' => "TRUE",
                'Message' => 'Data Added',
                'Data' => [$field]
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'Some Problems Occured! Please Try Again'
            ], 200);
        }
    }

    public function post_getVendorById()
    {
        $dboy_id = strip_tags($this->post('vendor_id'));
        $this->db->where('id', $dboy_id);
        $query = $this->db->get('vendor');
        $row = $query->row_array();
        if (!$row) {
            $data = array();
            $data1 = array();
            $data1['Status'] = "False";
            $data1['Message'] = "No Data Found";
            $data = $data1;
            $this->response($data, REST::HTTP_OK);
        } else {
            $this->db->where('id', $dboy_id);
            $query = $this->db->get('vendor');
            $data = $query->row_array();

            $partner['id'] = $data['id'];
            $partner['fullname'] = $data['fullname'];
            $partner['email'] = $data['email'];
            $partner['profile_pic'] = $data['profile_pic'];
            $partner['mobile'] = $data['mobile'];

            $this->response([
                "Status" => "TRUE",
                "Message" => "Data Found",
                "Data" => [$partner]
            ], REST::HTTP_OK);
        }
    }

    public function post_vendorAllProList()
    {

        $vendor_id = $this->post('vendor_id');

        $this->db->where('vendor_id', $vendor_id);
        $categories = $this->db->get('tbl_vendorproducts')->result_array();
        if (!empty($categories)) {

            foreach ($categories as $key => $val) {
                $cat['id'] = $val['id'];
                $cat['vendor_id'] = $val['vendor_id'];
                $cat['product_name'] = $val['product_name'];
                $cat['categoryid'] = $val['category'];
                $cat['sub_categoryid'] = $val['sub_category'];
                $cat['madein'] = $val['madein'];
                $cat['unit'] = $val['unit'];
                $cat['type'] = $val['type'];

                $this->db->where('categoryid', $val['category']);
                $vendor = $this->db->get('mst_category')->row_array();
                $cat['category_name'] = $vendor['category_name'];

                $this->db->where('id', $val['sub_category']);
                $vendor = $this->db->get('mst_subcategory')->row_array();
                $cat['subcategory'] = $vendor['name'];




                $cate[$key] = $cat;
            }
            $this->response([
                'Status' => "TRUE",
                'Message' => 'Data Availaible.',
                'Data' => $cate
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'No Data Found.',
                'Data' => $categories
            ], 200);
        }
    }

    public function post_vendorActiveProList()
    {

        $vendor_id = $this->post('vendor_id');

        $this->db->where('vendor_id', $vendor_id)->where('status', 1);
        $categories = $this->db->get('tbl_vendorproducts')->result_array();
        if (!empty($categories)) {

            foreach ($categories as $key => $val) {
                $cat['id'] = $val['id'];
                $cat['vendor_id'] = $val['vendor_id'];
                $cat['product_name'] = $val['product_name'];
                $cat['categoryid'] = $val['category'];
                $cat['sub_categoryid'] = $val['sub_category'];
                $cat['madein'] = $val['madein'];
                $cat['unit'] = $val['unit'];
                $cat['type'] = $val['type'];

                $this->db->where('categoryid', $val['category']);
                $vendor = $this->db->get('mst_category')->row_array();
                $cat['category_name'] = $vendor['category_name'];

                $this->db->where('id', $val['sub_category']);
                $vendor = $this->db->get('mst_subcategory')->row_array();
                $cat['subcategory'] = $vendor['name'];




                $cate[$key] = $cat;
            }
            $this->response([
                'Status' => "TRUE",
                'Message' => 'Data Availaible.',
                'Data' => $cate
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'No Data Found.',
                'Data' => $categories
            ], 200);
        }
    }

    public function post_vendorOutStockProList()
    {

        $vendor_id = $this->post('vendor_id');

        $this->db->where('vendor_id', $vendor_id)->where('status', 2);
        $categories = $this->db->get('tbl_vendorproducts')->result_array();
        if (!empty($categories)) {

            foreach ($categories as $key => $val) {
                $cat['id'] = $val['id'];
                $cat['vendor_id'] = $val['vendor_id'];
                $cat['product_name'] = $val['product_name'];
                $cat['categoryid'] = $val['category'];
                $cat['sub_categoryid'] = $val['sub_category'];
                $cat['madein'] = $val['madein'];
                $cat['unit'] = $val['unit'];
                $cat['type'] = $val['type'];

                $this->db->where('categoryid', $val['category']);
                $vendor = $this->db->get('mst_category')->row_array();
                $cat['category_name'] = $vendor['category_name'];

                $this->db->where('id', $val['sub_category']);
                $vendor = $this->db->get('mst_subcategory')->row_array();
                $cat['subcategory'] = $vendor['name'];




                $cate[$key] = $cat;
            }
            $this->response([
                'Status' => "TRUE",
                'Message' => 'Data Availaible.',
                'Data' => $cate
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'No Data Found.',
                'Data' => $categories
            ], 200);
        }
    }

    public function post_updateVendorProCost()
    {
        $id = $this->post('vendorproducts_id');
        $field['inventory'] = $this->post('inventory');
        $field['price'] = $this->post('price');
        $field['discount_price'] = $this->post('discount_price');

        $query = $this->db->get_where('tbl_vendorproducts', array('id' => $id));
        $row = $query->num_rows();

        if ($row > 0) {
            $this->db->where('id', $id);
            $this->db->update('tbl_vendorproducts', $field);
            $this->response([
                'Status' => "TRUE",
                'Message' => 'Data Has been Updated',
                'Data' => $field
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'Some Problems Occured! Please Try Again'
            ], 200);
        }
    }

    public function post_vendorProById()
    {

        $vendor_id = $this->post('vendorPro_id');

        $this->db->where('id', $vendor_id);
        $categories = $this->db->get('tbl_vendorproducts')->result_array();
        if (!empty($categories)) {

            foreach ($categories as $key => $val) {
                $cat['id'] = $val['id'];
                $cat['vendor_id'] = $val['vendor_id'];
                $cat['product_name'] = $val['product_name'];
                $cat['categoryid'] = $val['category'];
                $cat['sub_categoryid'] = $val['sub_category'];
                $cat['madein'] = $val['madein'];
                $cat['unit'] = $val['unit'];
                $cat['type'] = $val['type'];

                $this->db->where('categoryid', $val['category']);
                $vendor = $this->db->get('mst_category')->row_array();
                $cat['category_name'] = $vendor['category_name'];

                $this->db->where('id', $val['sub_category']);
                $vendor = $this->db->get('mst_subcategory')->row_array();
                $cat['subcategory'] = $vendor['name'];




                $cate[$key] = $cat;
            }
            $this->response([
                'Status' => "TRUE",
                'Message' => 'Data Availaible.',
                'Data' => $cate
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'No Data Found.',
                'Data' => $categories
            ], 200);
        }
    }

    public function post_addUserCart()
    {
        $field['userid'] = $this->post('userid');
        $field['product_name'] = $this->post('product_name');
        $field['product_price'] = $this->post('product_price');
        $field['amount'] = $this->post('amount');
        $field['item_total'] = $this->post('item_total');
        $field['total_discount'] = $this->post('total_discount');
        $field['delivery_fee'] = $this->post('delivery_fee');
        $field['total'] = $this->post('total');
        $field['coupon'] = $this->post('coupon');
        $field['address'] = $this->post('address');




        if (!empty($field)) {
            $this->db->insert('tbl_cart', $field);
            $this->response([
                'Status' => "TRUE",
                'Message' => 'Cart Has been Added',
                'Data' => $field
            ], 200);
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'Some Problems Occured! Please Try Again'
            ], 200);
        }
    }

    public function post_addUserWallet()
    {
        $user_id = $this->post('userid');
        $amount = $this->post('amount');

        $data = $this->db->get_where('tbl_wallet', array('userid' => $user_id))->row_array();

        if (!empty($data)) {

            $field['amount'] = $data['amount'] + $amount;

            $this->db->where('userid', $user_id);
            $update = $this->db->update('tbl_wallet', $field);


            $wal['userid'] = $user_id;
            $wal['add_amnt'] = $amount;
            $wal['total_amount'] = $field['amount'];


            if ($update > 0) {
                $this->response([
                    'Status' => "TRUE",
                    'Message' => 'Amount Has been Updated To Wallet',
                    'Data' => $wal
                ], REST::HTTP_OK);
            } else {
                $this->response([
                    'Status' => "FALSE",
                    'Message' => 'Some Error Occured! Please Try Again'
                ], REST::HTTP_OK);
            }
        } else if (empty($data)) {
            $field['userid'] = $user_id;
            $field['amount'] = $amount;

            $update = $this->db->insert('tbl_wallet', $field);

            if ($update > 0) {
                $this->response([
                    'Status' => "TRUE",
                    'Message' => 'Amount Has been Added To Wallet',
                    'Data' => $field
                ], REST::HTTP_OK);
            } else {
                $this->response([
                    'Status' => "FALSE",
                    'Message' => 'Some Error Occured! Please Try Again'
                ], REST::HTTP_OK);
            }
        } else {
            $this->response([
                'Status' => "FALSE",
                'Message' => 'Empty Fields Passed'
            ], REST::HTTP_OK);
        }
    }
}
