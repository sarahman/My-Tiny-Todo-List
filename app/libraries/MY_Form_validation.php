<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation
{
    public function __construct()
    {
        parent::__construct();
        $this->set_error_delimiters('', '');
    }

    public function setRulesForSignIn()
    {
        $config = array(
            array(
                'field' => 'username',
                'label' => 'Username',
                'rules' => 'required'
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'required|min_length[6]'
            )
        );

        $this->set_rules($config);
    }


    /* *********************** IMPORTANT *************************
    * This method is overridden to implement the callback methods
    * in this class instead of defining in individual controller.
    */
    function _execute($row, $rules, $postData = NULL, $cycles = 0)
    {
        // If the $_POST data is an array we will run a recursive call
        if (is_array($postData)) {
            foreach ($postData as $val) {
                $this->_execute($row, $rules, $val, $cycles);
                $cycles++;
            }

            return;
        }

        // --------------------------------------------------------------------
        // If the field is blank, but NOT required, no further tests are necessary
        $callback = FALSE;
        if (!in_array('required', $rules) AND is_null($postData)) {
            // Before we bail out, does the rule contain a callback?
            if (preg_match("/(callback_\w+)/", implode(' ', $rules), $match)) {
                $callback = TRUE;
                $rules = (array('1' => $match[1]));
            } else {
                return;
            }
        }

        // --------------------------------------------------------------------
        // Isset Test. Typically this rule will only apply to checkboxes.
        if (is_null($postData) AND $callback == FALSE) {
            if (in_array('isset', $rules, TRUE) OR in_array('required', $rules)) {
                // Set the message type
                $type = (in_array('required', $rules)) ? 'required' : 'isset';

                if (empty ($this->_error_messages[$type])) {
                    if (FALSE === ($line = $this->CI->lang->line($type))) {
                        $line = 'The field was not set';
                    }
                } else {
                    $line = $this->_error_messages[$type];
                }

                // Build the error message
                $message = sprintf($line, $this->_translate_fieldname($row['label']));

                // Save the error message
                $this->_field_data[$row['field']]['error'] = $message;

                if (empty ($this->_error_array[$row['field']])) {
                    $this->_error_array[$row['field']] = $message;
                }
            }

            return;
        }

        // --------------------------------------------------------------------
        // Cycle through each rule and run it
        foreach ($rules As $rule) {
            $_in_array = FALSE;

            // We set the $postData variable with the current data in our master array so that
            // each cycle of the loop is dealing with the processed data from the last cycle
            if ($row['is_array'] == TRUE AND is_array($this->_field_data[$row['field']]['postdata'])) {
                // We shouldn't need this safety, but just in case there isn't an array index
                // associated with this cycle we'll bail out
                if (empty ($this->_field_data[$row['field']]['postdata'][$cycles])) {
                    continue;
                }

                $postData = $this->_field_data[$row['field']]['postdata'][$cycles];
                $_in_array = TRUE;
            } else {
                $postData = $this->_field_data[$row['field']]['postdata'];
            }

            // --------------------------------------------------------------------
            // Is the rule a callback?
            $callback = FALSE;
            if (substr($rule, 0, 9) == 'callback_') {
                $rule = substr($rule, 9);
                $callback = TRUE;
            }

            // Strip the parameter (if exists) from the rule
            // Rules can contain a parameter: max_length[5]
            $param = FALSE;
            if (preg_match("/(.*?)\[(.*?)\]/", $rule, $match)) {
                $rule = $match[1];
                $param = $match[2];
            }

            // Call the function that corresponds to the rule
            if ($callback === TRUE) {
                if (method_exists($this->CI, $rule)) {

                    // Run the function and grab the result
                    $result = $this->CI->$rule($postData, $param);

                    // Re-assign the result to the master data array
                    if ($_in_array == TRUE) {
                        $this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postData : $result;
                    } else {
                        $this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postData : $result;
                    }

                    // If the field isn't required and we just processed a callback we'll move on...
                    if (!in_array('required', $rules, TRUE) AND $result !== FALSE) {
                        continue;
                    }
                } else if (method_exists($this, $rule)) {

                    $result = $this->$rule($postData, $param);

                    if ($_in_array == TRUE) {
                        $this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postData : $result;
                    } else {
                        $this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postData : $result;
                    }

                } else {
                    continue;
                }
            } else {
                if (!method_exists($this, $rule)) {
                    // If our own wrapper function doesn't exist we see if a native PHP function does.
                    // Users can use any native PHP function call that has one param.
                    if (function_exists($rule)) {
                        $result = $rule($postData);

                        if ($_in_array == TRUE) {
                            $this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postData : $result;
                        } else {
                            $this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postData : $result;
                        }
                    }

                    continue;
                }

                $result = $this->$rule($postData, $param);

                if ($_in_array == TRUE) {
                    $this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postData : $result;
                } else {
                    $this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postData : $result;
                }
            }

            // Did the rule test negatively?  If so, grab the error.
            if ($result === FALSE) {
                if (empty ($this->_error_messages[$rule])) {
                    if (FALSE === ($line = $this->CI->lang->line($rule))) {
                        $line = 'Unable to access an error message corresponding to your field name.';
                    }
                } else {
                    $line = $this->_error_messages[$rule];
                }

                // Is the parameter we are inserting into the error message the name
                // of another field?  If so we need to grab its "field label"
                if (isset($this->_field_data[$param]) AND isset($this->_field_data[$param]['label'])) {
                    $param = $this->_field_data[$param]['label'];
                }

                // Build the error message
                $message = sprintf($line, $this->_translate_fieldname($row['label']), $param);

                // Save the error message
                $this->_field_data[$row['field']]['error'] = $message;

                if (!isset($this->_error_array[$row['field']])) {
                    $this->_error_array[$row['field']] = $message;
                }

                return;
            }
        }
    }
}