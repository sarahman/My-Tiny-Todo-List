<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" :
 * <thepixeldeveloper@googlemail.com> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return Mathew Davies
 * ----------------------------------------------------------------------------
*/

/**
 * redux_auth_model
 */
class redux_auth_model extends CI_Model
{
    /**
     * Holds an array of tables used in
     * redux.
     *
     * @var string
     **/
    public $tables = array();

    /**
     * activation code
     *
     * @var string
     **/
    public $activation_code;

    /**
     * forgotten password key
     *
     * @var string
     **/
    public $forgotten_password_code;

    /**
     * new password
     *
     * @var string
     **/
    public $new_password;

    /**
     * Identity
     *
     * @var string
     **/
    public $identity;

    public function __construct()
    {
        parent::__construct();
        $this->load->config('redux_auth');
        $this->tables  = $this->config->item('tables');
        $this->columns = $this->config->item('columns');
    }

    /**
     * Misc functions
     *
     * Hash password : Hashes the password to be stored in the database.
     * Hash password db : This function takes a password and validates it
     * against an entry in the users table.
     * Salt : Generates a random salt value.
     *
     * @author Mathew
     */

    /**
     * Hashes the password to be stored in the database.
     *
     * @return void
     * @author Mathew
     **/
    public function hash_password($password = false)
    {
        $salt_length = $this->config->item('salt_length');

        if ($password === false) {
            return false;
        }

        $salt = $this->salt();
        $password = $salt . substr(sha1($salt . $password), 0, -$salt_length);

        return $password;
    }

    /**
     * This function takes a password and validates it
     * against an entry in the users table.
     *
     * @return void
     * @author Mathew
     **/
    public function hash_password_db($identity = false, $password = false)
    {
        $identity_column   = $this->config->item('identity');
        $users_table       = $this->tables['users'];
        $salt_length       = $this->config->item('salt_length');

        if ($identity === false || $password === false) {
            return false;
        }

        $query = $this->db->select('password')
                          ->where($identity_column, $identity)
                          ->limit(1)
                          ->get($users_table);

        $result = $query->row();

        if ($query->num_rows() !== 1) {
            return false;
        }

        $salt = substr($result->password, 0, $salt_length);
        $password = $salt . substr(sha1($salt . $password), 0, -$salt_length);

        return $password;
    }

    /**
     * Generates a random salt value.
     *
     * @return void
     * @author Mathew
     **/
    public function salt()
    {
        return substr(md5(uniqid(rand(), true)), 0, $this->config->item('salt_length'));
    }

    /**
     * Activation functions
     *
     * Activate : Validates and removes activation code.
     * Deactivae : Updates a users row with an activation code.
     *
     * @author Mathew
     */

    /**
     * activate
     *
     * @return void
     * @author Mathew
     **/
    public function activate($code = false)
    {
        $identity_column = $this->config->item('identity');
        $users_table     = $this->tables['users'];

        if ($code === false) {
            return false;
        }

        $query = $this->db->select($identity_column)
                          ->where('activation_code', $code)
                          ->limit(1)
                          ->get($users_table);

        $result = $query->row();

        if ($query->num_rows() !== 1) {
            return false;
        }

        $identity = $result->{$identity_column};

        $data = array('activation_code' => '','status'=>'approved');

        $this->db->update($users_table, $data, array($identity_column => $identity));
        $this->session->set_userdata('account_status', 'activated');

        return ($this->db->affected_rows() == 1) ? true : false;
    }

    /**
     * Deactivate
     *
     * @return void
     * @author Mathew
     **/
    public function deactivate($username = false)
    {
        $users_table = $this->tables['users'];

        if ($username === false) {
            return false;
        }

        $activation_code = sha1(md5(microtime()));
        $this->activation_code = $activation_code;

        $data = array('activation_code' => $activation_code);
        $this->db->update($users_table, $data, array('username' => $username));

        return ($this->db->affected_rows() == 1) ? true : false;
    }

    /**
     * change password
     *
     * @return void
     * @author Mathew
     **/
    public function change_password($identity = false, $old = false, $new = false)
    {
        $identity_column   = $this->config->item('identity');
        $users_table       = $this->tables['users'];

        if ($identity === false || $old === false || $new === false) {
            return false;
        }

        $query = $this->db->select('password')
                          ->where($identity_column, $identity)
                          ->limit(1)
                          ->get($users_table);

        $result = $query->row();

        $db_password = $result->password;
        $old         = $this->hash_password_db($identity, $old);
        $new         = $this->hash_password($new);

        if ($db_password === $old) {

            $data = array('password' => $new);
            $this->db->update($users_table, $data, array($identity_column => $identity));

            return ($this->db->affected_rows() == 1) ? true : false;
        }

        return false;
    }

    /**
     * Checks username.
     *
     * @return void
     * @author Mathew
     **/
    public function username_check($username = false)
    {
        $users_table = $this->tables['users'];

        if ($username === false) {
            return false;
        }

        $query = $this->db->select('id')
                          ->where('username', $username)
                          ->limit(1)
                          ->get($users_table);

        if ($query->num_rows() == 1) {
            return true;
        }

        return false;
    }

    /**
     * Checks email.
     *
     * @return void
     * @author Mathew
     **/
    public function email_check($email = false)
    {
        $users_table = $this->tables['users'];

        if ($email === false) {
            return false;
        }

        $query = $this->db->select('id')
                          ->where('email', $email)
                          ->limit(1)
                          ->get($users_table);

        if ($query->num_rows() == 1) {
            return true;
        }

        return false;
    }

    /**
     * Identity check
     *
     * @return void
     * @author Mathew
     **/
    protected function identity_check($identity = false)
    {
        $identity_column = $this->config->item('identity');
        $users_table     = $this->tables['users'];

        if ($identity === false) {
            return false;
        }

        $query = $this->db->select('id')
                ->where($identity_column, $identity)
                ->limit(1)
                ->get($users_table);

        if ($query->num_rows() == 1)
        {
            return true;
        }

        return false;
    }

    /**
     * Insert a forgotten password key.
     *
     * @return void
     * @author Mathew
     **/
    public function forgotten_password($email = false)
    {
        $users_table = $this->tables['users'];
        $identity_column = $this->config->item('identity');

        if ($email === false)
        {
            return false;
        }

        $query = $this->db->select('forgotten_password_code')
                ->where('email', $email)
                ->limit(1)
                ->get($users_table);

        $result = $query->row();
        if(!$result)
        {
            return false;
        }
        $code = $result->forgotten_password_code;

        if (empty($code))
        {
            $key = $this->hash_password(microtime().$email);

            $this->forgotten_password_code = $key;

            $data = array('forgotten_password_code' => $key);

            $this->db->update($users_table, $data, array('email' => $email));

            return ($this->db->affected_rows() == 1) ? true : false;
        }
        else
        {
            return false;
        }
    }

    /**
     * undocumented function
     *
     * @return void
     * @author Mathew
     **/
    public function forgotten_password_complete($code = false)
    {
        $users_table = $this->tables['users'];
        $identity_column = $this->config->item('identity');

        if ($code === false)
        {
            return false;
        }

        $query = $this->db->select("id , $identity_column")
                ->where('forgotten_password_code', $code)
                ->limit(1)
                ->get($users_table);

        $result = $query->row();

        if ($query->num_rows() > 0)
        {
            $salt       = $this->salt();
            $password   = $this->hash_password($salt);

            $this->new_password = $salt;

            $data = array('password'                => $password,
                    'forgotten_password_code' => '0');

            $this->db->update($users_table, $data, array('forgotten_password_code' => $code));

            $this->session->set_userdata($identity_column,  $result->{$identity_column});
            $this->session->set_userdata('user_id',         $result->id);

            return true;
        }

        return false;
    }

    /**
     * profile
     *
     * @return void
     * @author Mathew
     **/
    public function profile($identity = false)
    {
        $users_table     = $this->tables['users'];
        $groups_table    = $this->tables['groups'];
        $meta_table      = $this->tables['meta'];
        $meta_join       = $this->config->item('join');
        $identity_column = $this->config->item('identity');

        if ($identity === false)
        {
            return false;
        }

        $this->db->select($users_table.'.id, '.
                $users_table.'.username, ' .
                $users_table.'.password, '.
                $users_table.'.email, '.
                $meta_table .'.full_name, '.
                $meta_table .'.phone, '.
                $users_table.'.activation_code, '.
                $users_table.'.forgotten_password_code , '.
                $users_table.'.ip_address, '.
                $groups_table.'.name AS `group`');

        if (!empty($this->columns))
        {
            foreach ($this->columns as $value)
            {
                $this->db->select($meta_table.'.'.$value);
            }
        }

        $this->db->from($users_table);
        $this->db->join($meta_table, $users_table.'.id = '.$meta_table.'.'.$meta_join, 'left');
        $this->db->join($groups_table, $users_table.'.group_id = '.$groups_table.'.id', 'left');

        if (strlen($identity) === 40)
        {
            $this->db->where($users_table.'.forgotten_password_code', $identity);
        }
        else
        {
            $this->db->where($users_table.'.'.$identity_column, $identity);
        }

        $this->db->limit(1);
        $i = $this->db->get();

        return ($i->num_rows > 0) ? $i->row() : false;
    }

    /**
     * profile
     *
     * @return void
     * @author Mathew
     **/
    public function profileByField($field = FALSE , $value = FALSE)
    {
        $users_table     = $this->tables['users'];
        $identity_column = $this->config->item('identity');

        if ($field === FALSE || $value === FALSE)
        {
            return false;
        }

        $this->db->select($users_table.'.'.$identity_column);
        $this->db->from($users_table);
        $this->db->where("$field = '$value'");
        $this->db->limit(1);
        $query = $this->db->get();
        $rows = $query->result_array();

        return count($rows)? $this->profile($rows[0][$identity_column]): false;
    }

    /**
     * Basic functionality
     *
     * Register
     * Login
     *
     * @author Mathew
     */

    /**
     * register
     *
     * @return void
     * @author Mathew
     **/
    public function register($username = false, $password = false, $email = false)
    {
        $users_table        = $this->tables['users'];
        $meta_table         = $this->tables['meta'];
        $groups_table       = $this->tables['groups'];
        $meta_join          = $this->config->item('join');
        $additional_columns = $this->config->item('columns');

        if ($username === false || $password === false || $email === false)
        {
            return false;
        }

        // Group ID
        $query    = $this->db->select('id')->where('name', $this->config->item('default_group'))->get($groups_table);
        $result   = $query->row();
        $group_id = isset($result->id) ? $result->id : 1 ;

        // IP Address
        $ip_address = $this->input->ip_address();

        $password = $this->hash_password($password);

        // Users table.
        $data = array('username' => $username,
                'password' => $password,
                'email'    => $email,
                'group_id' => $group_id,
                'create_date' => date("Y-m-d H:i:s"),
                'ip_address' => $ip_address);

        $this->db->insert($users_table, $data);

        // Meta table.
        $id = $this->db->insert_id();

        $data = array($meta_join => $id);

        if (!empty($additional_columns))
        {
            foreach ($additional_columns as $input)
            {
                $data[$input] = ($this->input->post($input) !== false) ? $this->input->post($input) : null;
            }
        }

        $this->db->insert($meta_table, $data);

        return ($this->db->affected_rows() > 0) ? true : false;
    }

    /**
     * login
     *
     * @return void
     * @author Mathew
     **/
    public function login($identity = false, $password = false)
    {
        $identity_column = $this->config->item('identity');
        $users_table     = $this->tables['users'];

        if ($identity === false || $password === false || $this->identity_check($identity) == false)
        {
            return false;
        }
        $query = $this->db->select($identity_column.',email,  username, status, password, activation_code, id')
                ->where($identity_column, $identity)
                ->limit(1)
                ->get($users_table);

        $result = $query->row();
        if ($query->num_rows() == 1)
        {
            $password = $this->hash_password_db($identity, $password);

            if (!empty($result->activation_code))
            {
                $this->session->set_userdata('account_status', 'not activated');
                $this->session->set_userdata('username',    $result->username);
                return false;

            }
            if ($result->password === $password)
            {
                $this->session->set_userdata('email',       $result->email);
                $this->session->set_userdata('username',    $result->username);
                $this->session->set_userdata('user_id',     $result->id);
                $this->session->set_userdata('status',      $result->status);

                return true;
            }
        }

        return false;
    }

    /**
     * Update meta
     *
     * @return boolean
     * @author Md Emran Hasan
     **/
    public function updateMeta($data = array(), $identity = false)
    {
        if (empty($data) || !$identity)
        {
            return false;
        }

        $users_table     = $this->tables['users'];
        $meta_table      = $this->tables['meta'];
        $meta_join       = $this->config->item('join');

        $identity_column = $this->config->item('identity');
        $additional_columns = $this->config->item('columns');

        $this->db->select($users_table.'.id');
        $this->db->where($users_table.'.'.$identity_column, $identity);
        $this->db->limit(1);

        $result = $this->db->get($users_table);

        if ($result->num_rows == 0)
        {
            return false;
        }

        $row = $result->row();
        $id = $row->id;

        if (!empty($additional_columns))
        {
            foreach ($additional_columns as $input)
            {
                if(isset($data[$input]))
                    $updateData[$input] = $data[$input];
            }
        }

        $this->db->update($meta_table, $updateData, array($meta_join => $id));

        return ($this->db->affected_rows() > 0) ? true : false;
    }
}
