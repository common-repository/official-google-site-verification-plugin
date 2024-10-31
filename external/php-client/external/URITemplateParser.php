<?php
/**
 * This file is part of the Google Verification Wordpress Plugin.
 *
 * The Google Verification Wordpress Plugin is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation,
 * either version 2 of the License, or (at your option) any later version.

 * The Google Site Verification Wordpress Plugin is distributed in the hope
 * that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with the Google Site Verification Wordpress Plugin.
 * If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * A URI Template Parser which is used by the apiREST class to resolve the REST requests
 * Blogpost: http://lab.kevburnsjr.com/php-uri-template-parser
 * Source: http://github.com/KevBurnsJr/php-uri-template-parser
 */
class URI_Template_Parser {

  public static $operators = array('+', ';', '?', '/', '.');
  public static $reserved_operators = array('|', '!', '@');
  public static $explode_modifiers = array('+', '*');
  public static $partial_modifiers = array(':', '^');

  public static $gen_delims = array(':', '/', '?', '#', '[', ']', '@');
  public static $gen_delims_pct = array('%3A', '%2F', '%3F', '%23', '%5B', '%5D', '%40');
  public static $sub_delims = array('!', '$', '&', '\'', '(', ')', '*', '+', ',', ';', '=');
  public static $sub_delims_pct = array('%21', '%24', '%26', '%27', '%28', '%29', '%2A', '%2B', '%2C', '%3B', '%3D');
  public static $reserved;
  public static $reserved_pct;

  public function __construct($template) {
    self::$reserved = array_merge(self::$gen_delims, self::$sub_delims);
    self::$reserved_pct = array_merge(self::$gen_delims_pct, self::$sub_delims_pct);
    $this->template = $template;
  }

  public function expand($data) {
    // Modification to make this a bit more performant (since gettype is very slow)
    if (! is_array($data)) {
      $data = (array)$data;
    }
    /*
    // Original code, which uses a slow gettype() statement, kept in place for if the assumption that is_array always works here is incorrect
    switch (gettype($data)) {
      case "boolean":
      case "integer":
      case "double":
      case "string":
      case "object":
        $data = (array)$data;
        break;
    }
*/

    // Resolve template vars
    preg_match_all('/\{([^\}]*)\}/', $this->template, $em);

    foreach ($em[1] as $i => $bare_expression) {
      preg_match('/^([\+\;\?\/\.]{1})?(.*)$/', $bare_expression, $lm);
      $exp = new StdClass();
      $exp->expression = $em[0][$i];
      $exp->operator = $lm[1];
      $exp->variable_list = $lm[2];
      $exp->varspecs = explode(',', $exp->variable_list);
      $exp->vars = array();
      foreach ($exp->varspecs as $varspec) {
        preg_match('/^([a-zA-Z0-9_]+)([\*\+]{1})?([\:\^][0-9-]+)?(\=[^,]+)?$/', $varspec, $vm);
        $var = new StdClass();
        $var->name = $vm[1];
        $var->modifier = isset($vm[2]) && $vm[2] ? $vm[2] : null;
        $var->modifier = isset($vm[3]) && $vm[3] ? $vm[3] : $var->modifier;
        $var->default = isset($vm[4]) ? substr($vm[4], 1) : null;
        $exp->vars[] = $var;
      }

      // Add processing flags
      $exp->reserved = false;
      $exp->prefix = '';
      $exp->delimiter = ',';
      switch ($exp->operator) {
        case '+':
          $exp->reserved = 'true';
          break;
        case ';':
          $exp->prefix = ';';
          $exp->delimiter = ';';
          break;
        case '?':
          $exp->prefix = '?';
          $exp->delimiter = '&';
          break;
        case '/':
          $exp->prefix = '/';
          $exp->delimiter = '/';
          break;
        case '.':
          $exp->prefix = '.';
          $exp->delimiter = '.';
          break;
      }
      $expressions[] = $exp;
    }

    // Expansion
    $this->expansion = $this->template;

    foreach ($expressions as $exp) {
      $part = $exp->prefix;
      $exp->one_var_defined = false;
      foreach ($exp->vars as $var) {
        $val = '';
        if ($exp->one_var_defined && isset($data[$var->name])) {
          $part .= $exp->delimiter;
        }
        // Variable present
        if (isset($data[$var->name])) {
          $exp->one_var_defined = true;
          $var->data = $data[$var->name];

          $val = self::val_from_var($var, $exp);

        // Variable missing
        } else {
          if ($var->default) {
            $exp->one_var_defined = true;
            $val = $var->default;
          }
        }
        $part .= $val;
      }
      if (! $exp->one_var_defined) $part = '';
      $this->expansion = str_replace($exp->expression, $part, $this->expansion);
    }

    return $this->expansion;
  }

  private function val_from_var($var, $exp) {
    $val = '';
    if (is_array($var->data)) {
      $i = 0;
      if ($exp->operator == '?' && ! $var->modifier) {
        $val .= $var->name . '=';
      }
      foreach ($var->data as $k => $v) {
        $del = $var->modifier ? $exp->delimiter : ',';
        $ek = rawurlencode($k);
        $ev = rawurlencode($v);

        // Array
        if ($k !== $i) {
          if ($var->modifier == '+') {
            $val .= $var->name . '.';
          }
          if ($exp->operator == '?' && $var->modifier || $exp->operator == ';' && $var->modifier == '*' || $exp->operator == ';' && $var->modifier == '+') {
            $val .= $ek . '=';
          } else {
            $val .= $ek . $del;
          }

        // List
        } else {
          if ($var->modifier == '+') {
            if ($exp->operator == ';' && $var->modifier == '*' || $exp->operator == ';' && $var->modifier == '+' || $exp->operator == '?' && $var->modifier == '+') {
              $val .= $var->name . '=';
            } else {
              $val .= $var->name . '.';
            }
          }
        }
        $val .= $ev . $del;
        $i ++;
      }
      $val = trim($val, $del);

    // Strings, numbers, etc.
    } else {
      if ($exp->operator == '?') {
        $val = $var->name . (isset($var->data) ? '=' : '');
      } else if ($exp->operator == ';') {
        $val = $var->name . ($var->data ? '=' : '');
      }
      $val .= rawurlencode($var->data);
      if ($exp->operator == '+') {
        $val = str_replace(self::$reserved_pct, self::$reserved, $val);
      }
    }
    return $val;
  }

  public function match($uri) {}

  public function __toString() {
    return $this->template;
  }
}
