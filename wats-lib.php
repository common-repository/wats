<?php

/*****************************************/
/*                                       */
/* Fonction de log d'un message de debug */
/*                                       */
/*****************************************/

function wats_debug($msg)
{
	if (WATS_DEBUG)
	{
	    $today = date("d/m/Y H:i:s ");
	    $myFile = dirname(__file__) . "/debug.log";
	    $fh = fopen($myFile, 'a') or die("Can't open debug file. Please manually create the 'debug.log' file (inside the 'wats' directory) and make it writable.");
	    $ua_simple = preg_replace("/(.*)\s\(.*/","\\1",$_SERVER['HTTP_USER_AGENT']);
	    fwrite($fh, $today . " [from: ".$_SERVER['REMOTE_ADDR']."|$ua_simple] - " . $msg . "\n");
	    fclose($fh);
	}
}

/*******************************************/
/*                                         */
/* Fonction de vérification d'un numérique */
/*                                         */
/*******************************************/

function wats_is_numeric($i)
{
	return (preg_match("/^\d+$/", $i));
}

/*****************************************/
/*                                       */
/* Fonction de vérification d'une chaîne */
/*                                       */
/*****************************************/

function wats_is_string($i)
{
	$temp = wp_strip_all_tags($i);
	if (strlen($temp) != strlen($i))
		return 0;

	if (WATS_VERIFY_STRING)
		return (preg_match("/^[\.\,\#\&\;\'\’\"\+\-\_\:\/?!()<>°*ß@ÀÁÂÃÄÅÇČĎĚÈÉÊËÌÍÎÏŇÒÓÔÕÖŘŠŤÙÚÛÜŮÝŽکگچپژیàáâãäåçčďěèéêëìíîïňðòóôõöřšťùúûüůýÿžدجحخهعغفقثصضطكمنتاأللأبيسشظزوةىآلالآرؤءئa-zA-Z-\d ]+$/", $i));
	else
		return 1;
}

/**************************************************************/
/*                                       					  */
/* Fonction de vérification d'une chaîne avec carriage return */
/*                                                            */
/**************************************************************/

function wats_is_paragraph($i)
{
	$temp = wp_strip_all_tags($i);
	if (strlen($temp) != strlen($i))
		return 0;
	
	if (WATS_VERIFY_STRING)
		return (preg_match("/^[\.\,\#\&\;\'\’\"\+\-\_\:\/?!()<>°*ß@ÀÁÂÃÄÅÇČĎĚÈÉÊËÌÍÎÏŇÒÓÔÕÖŘŠŤÙÚÛÜŮÝŽکگچپژیàáâãäåçčďěèéêëìíîïňðòóôõöřšťùúûüůýÿžدجحخهعغفقثصضطكمنتاأللأبيسشظزوةىآلالآرؤءئa-zA-Z-\d\s ]+$/", $i));
	else
		return 1;
}

/****************************************************/
/*                                                  */
/* Fonction de vérification d'une lettre ou chiffre */
/*                                                  */
/****************************************************/

function wats_is_letter_or_number($i)
{

	return (preg_match("/^[a-zA-Z\d]+$/", $i));
}

/*****************************************/
/*                                       */
/* Fonction de vérification d'une lettre */
/*                                       */
/*****************************************/

function wats_is_letter($i)
{
	return (preg_match("/^[a-zA-Z]+$/", $i));
}

/***************************************/
/*                                     */
/* Fonction de vérification d'une date */
/* format 1 : yyyy-mm-dd               */
/* format 2 : mm/dd/yyyy               */
/*                                     */
/***************************************/

function wats_is_date($i,$format)
{
	if ($format == 1)
		return (preg_match("#^(\d{4})\-(\d{2})\-(\d{2})$#", $i));
	else
		return (preg_match("#^(\d{2})\/(\d{2})\/(\d{4})$#", $i));	
}

/*************************************************************/
/*                                                           */
/* Fonction pour remplir une drop down list sans echo direct */
/*                                                           */
/*************************************************************/

function wats_fill_drop_down_no_echo($type_array,$selected_value)
{
	$output = '';

    foreach ($type_array as $key => $value)
    {
		$output .= '<option value="'.esc_attr($key).'"';
        if ($selected_value == $key) $output .= ' selected';
		$output .= '>'.esc_html($value).'</option>';
	}

    return $output;
}

/***************************************************/
/*                                                 */
/* Fonction de construction d'un tableau numérique */
/*                                                 */
/***************************************************/

function wats_build_numeric_array($first,$last)
{
	$temptable = array();
	for ($i = $first; $i <= $last; $i++)
	{
		$temptable[$i] = $i;
	}
	
	return $temptable;
}

/******************************************/
/*                                        */
/* Fonction de calcul du numéro de ticket */
/*                                        */
/******************************************/

function wats_get_ticket_number($postID)
{
	global $wats_settings;
	
	$number = get_post_meta($postID,'wats_ticket_number',true);
	if (($wats_settings['numerotation'] == 0) || !$number)
		$value = 0;
	else if ($wats_settings['numerotation'] == 1)
	{
		$padding = '';
		if ($number < 10)
			$padding = '0000';
		else if ($number < 100)
			$padding = '000';
		else if ($number < 1000)
			$padding = '00';
		else if ($number < 10000)
			$padding = '0';
		$value = get_the_time("ymd",$postID)."-".$padding.$number;
	}
	else if ($wats_settings['numerotation'] == 2)
		$value = $number;
	
	$value = apply_filters('wats_filter_get_ticket_number',$value);
	

	return $value;
}

/********************************************************/
/*                                                      */
/* Fonction de récupération du dernier numéro de ticket */
/*                                                      */
/********************************************************/

function wats_get_latest_ticket_number()
{
	global $wpdb;
	
	$value = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = %s ORDER BY ABS(meta_value) DESC LIMIT 0,1",'wats_ticket_number'));

	if (!$value)
		$value = 0;
	
	return $value;
}

/*******************************************/
/*                                         */
/* Fonction de calcul du nombre de tickets */
/*                                         */
/*******************************************/

function wats_get_number_of_tickets()
{
	global $wpdb;
	
	$value = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->postmeta WHERE meta_key = %s",'wats_ticket_number'));

	if (!$value)
		$value = 0;
	
	return $value;
}

/*********************************************************************/
/*                                                                   */
/* Fonction de calcul du nombre de tickets par status et utilisateur */
/*                                                                   */
/*********************************************************************/

function wats_get_number_of_tickets_by_status($status,$userid)
{
	global $wpdb;
	
	$query = "SELECT COUNT(*) FROM $wpdb->posts";
	if ($status != 0)
		$query .= " LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) WHERE $wpdb->posts.post_type = %s AND $wpdb->postmeta.meta_key = 'wats_ticket_status' AND $wpdb->postmeta.meta_value = $status AND $wpdb->posts.post_status = 'publish'";
	else
		$query .= " WHERE $wpdb->posts.post_type = %s AND $wpdb->posts.post_status = 'publish'";
		
	if ($userid != 0)
		$query .= " AND $wpdb->posts.post_author = $userid";

	$value = $wpdb->get_var($wpdb->prepare($query,'ticket'));

	if (!$value)
		$value = 0;
	
	return $value;
}

/*****************************************************/
/*                                                   */
/* Fonction de récupération de l'ID du status Closed */
/*                                                   */
/*****************************************************/

function wats_get_closed_status_id()
{
	global $wats_settings;
	
	return $wats_settings['closed_status_id'];
}

/*****************************************************/
/*                                                   */
/* Fonction de remplissage de la table des capacités */
/*                                                   */
/*****************************************************/

function wats_init_capabilities_table()
{
	global $wp_roles, $current_user, $wats_settings;
	
	$wats_capabilities_table = array();
	$wats_capabilities_table['wats_ticket_ownership'] = __('Tickets can be assigned to this user','WATS');
	if ($wats_settings['ticket_visibility_read_only_capability'] == 1)
		$wats_capabilities_table['wats_ticket_read_only'] = __('All tickets can be browsed by this user (read only access)','WATS');

	foreach ($wp_roles->role_names as $roledesc => $rolename)
	{
		if (in_array($roledesc,array_values($current_user->roles)))
		{
			$role = $wp_roles->get_role($roledesc);
			if (!array_key_exists('upload_files',$role->capabilities))
				$wats_capabilities_table['upload_files'] = __('User can attach files to tickets','WATS');
		}
	}
	
	return ($wats_capabilities_table);
}

/******************************************************/
/*                                                    */
/* Fonction de récupération du login du premier admin */
/*                                                    */
/******************************************************/

function wats_get_first_admin_login()
{
    global $wpdb;

	$users = $wpdb->get_results("SELECT ID FROM $wpdb->users");
	
	foreach ($users AS $user)
	{
		$user = new WP_user($user->ID);
		if ($user->has_cap('administrator'))
			return ($user->user_login);
	}		
	
	return 0;
}

/****************************************************/
/*                                                  */
/* Fonction de récupération du nom à partir d'un ID */
/*                                                  */
/****************************************************/

function wats_get_full_name($id)
{
	return (get_user_meta($id,"first_name",true)." ".get_user_meta($id,"last_name",true));
}


/**************************************************************/
/*                                                            */
/* Fonction Ajax de construction de la liste des utilisateurs */
/*                                                            */
/**************************************************************/

function wats_ajax_get_user_list($searched_value,$required_capability,$excluded_user_list,$included_user_list)
{
	global $wpdb, $wats_settings;
	
	wats_load_settings();
	
	$metakeylist = wats_get_list_of_user_meta_keys(1);
	foreach ($metakeylist AS $index => $metakey)
	{
		if (strpos($wats_settings['user_selector_format'],$metakey) === false)
			unset($metakeylist[$index]);
	}

	$join = '';
	$where = '';
	$x = 0;
	foreach ($metakeylist AS $index => $metakey)
	{
		$x++;
		$join .= " LEFT JOIN $wpdb->usermeta AS wp".$x." ON wp0.ID = wp".$x.".user_id";
		$where .= " OR (wp".$x.".meta_key = '".$metakey."' AND wp".$x.".meta_value LIKE '%%".esc_sql($wpdb->esc_like($searched_value))."%%')";
	}

	$limit = " LIMIT 0,".WATS_MAX_NUMBER_OF_RESULTS_FOR_AUTO_COMPLETE;
	$users = $wpdb->get_results("SELECT DISTINCT user_login, ID FROM $wpdb->users as wp0 ".$join." WHERE wp0.user_login LIKE '%%".esc_sql($wpdb->esc_like($searched_value))."%%' ".$where.$limit);

	$list = array();
	foreach ($users as $user)
	{
		$user = new WP_user($user->ID);
		if (!in_array($user->ID,$excluded_user_list,true))
		{
			$has_cap = 0;
			if (is_array($required_capability))
			foreach ($required_capability as $key => $capability)
			{
				if ($user->has_cap($capability))
					$has_cap = 1;
			}
			
			if ((!is_array($required_capability) && $required_capability === 0) || $has_cap == 1 || in_array("$user->ID",$included_user_list,true))
			{
				$output = $wats_settings['user_selector_format'];
				foreach ($metakeylist AS $metakey)
				{
					if (strpos($wats_settings['user_selector_format'],$metakey) !== false)
						$output = str_replace($metakey,get_user_meta($user->ID,$metakey,true),$output);
				}
				$output = str_replace('user_login',$user->user_login,$output);
				if (wats_is_string(stripslashes($output)))
					$entry['label'] = esc_html(stripslashes($output));
				else
					$entry['label'] = $user->user_login;
				$entry['value'] = $user->user_login;
				array_push($list,$entry);
			}
		}
	}
	
	return ($list);
}

/*********************************************************/
/*                                                       */
/* Fonction de construction de la liste des utilisateurs */
/*                                                       */
/*********************************************************/

function wats_build_user_list($firstitem,$cap)
{
    global $wpdb, $wats_settings;

	wats_load_settings();
	
	$order1 = $wats_settings['user_selector_order_1'];
	$order2 = $wats_settings['user_selector_order_2'];
	
    $users = $wpdb->get_results($wpdb->prepare("SELECT ID FROM $wpdb->users LEFT JOIN $wpdb->usermeta AS wp1 ON ($wpdb->users.ID = wp1.user_id AND wp1.meta_key = %s) LEFT JOIN $wpdb->usermeta AS wp2 ON ($wpdb->users.ID = wp2.user_id AND wp2.meta_key = %s) ORDER BY wp1.meta_value, wp2.meta_value",$order1,$order2));
    $userlist = array();
	if ($firstitem !== 0)
		$userlist[0] = $firstitem;
	
	$metakeylist = wats_get_list_of_user_meta_keys(1);
	foreach ($metakeylist AS $index => $metakey)
	{
		if (strpos($wats_settings['user_selector_format'],$metakey) === false)
			unset($metakeylist[$index]);
	}
	
	foreach ($users AS $user)
    {
		$user = new WP_user($user->ID);
		if ($cap === 0 || $user->has_cap($cap))
		{
			$output = $wats_settings['user_selector_format'];
			foreach ($metakeylist AS $metakey)
			{
				if (strpos($wats_settings['user_selector_format'],$metakey) !== false)
					$output = str_replace($metakey,get_user_meta($user->ID,$metakey,true),$output);
			}
			$output = str_replace('user_login',$user->user_login,$output);
			if (wats_is_string(stripslashes($output)))
				$userlist[$user->user_login] = esc_html(stripslashes($output));
			else
				$userlist[$user->user_login] = $user->user_login;
		}
	}
        
    return ($userlist);
}

/*******************************************/
/*                                         */
/* Fonction de construction du nom formaté */
/*                                         */
/*******************************************/

function wats_build_formatted_name($ID)
{
    global $wpdb, $wats_settings;

    $userlist = array();
	$metakeylist = wats_get_list_of_user_meta_keys(1);
	foreach ($metakeylist AS $index => $metakey)
	{
		if (strpos($wats_settings['user_selector_format'],$metakey) === false)
			unset($metakeylist[$index]);
	}
	
	$user = new WP_user($ID);
	$output = $wats_settings['user_selector_format'];
	foreach ($metakeylist AS $metakey)
	{
		if (strpos($wats_settings['user_selector_format'],$metakey) !== false)
			$output = str_replace($metakey,get_user_meta($user->ID,$metakey,true),$output);
	}
	$output = str_replace('user_login',$user->user_login,$output);
	if (wats_is_string(stripslashes($output)))
		$userlist[$user->user_login] = esc_html(stripslashes($output));
	else
		$userlist[$user->user_login] = $user->user_login;

    return ($userlist);
}

/*******************************************************/
/*                                                     */
/* Fonction de remplissage de la liste des meta keys */
/* view 0 : string contenant les meta keys           */
/* view 1 : table contenant les meta keys            */
/*                                                     */
/*******************************************************/

function wats_get_list_of_user_meta_keys($view)
{
	global $wpdb;
	
	$keys = $wpdb->get_results("SELECT DISTINCT meta_key FROM $wpdb->usermeta");
	if ($view == 0)
	{
		$list = '';
		$x = 0;
		foreach ($keys AS $key)
		{
			if (wats_is_string($key->meta_key))
			{
				if ($x != 0)
					$list .= ', ';
				else
					$x = 1;
				$list .= $key->meta_key;
			}
		}
		$list .= '.';
	}
	else
	{
		$list = array();
		foreach ($keys AS $key)
		{
			if (wats_is_string($key->meta_key))
				$list[] = $key->meta_key;
		}
	}
	
	return($list);
}

/*******************************************************/
/*                                                     */
/* Fonction de remplissage de la liste des meta values */
/*                                                     */
/*******************************************************/

function wats_build_list_meta_values($key)
{
	global $wpdb;
	
	$values = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT meta_value FROM $wpdb->usermeta WHERE meta_key=%s ORDER BY meta_value ASC",$key));
	$list = array();
	foreach ($values AS $value)
	{
		$list[] = $value->meta_value;
	}
	
	return($list);
}

/******************************************************/
/*                                                    */
/* Fonction de récupération de l'ID à partir du login */
/*                                                    */
/******************************************************/

function wats_get_user_ID_from_user_login($login)
{
	global $wpdb;
	
	$id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_login=%s",$login));
	
	return($id);
}

/******************************************************/
/*                                                    */
/* Fonction de modification du code des single quotes */
/*                                                    */
/******************************************************/

function wats_fix_single_quotes($str)
{
	
	$str = str_replace('#039;','#39;',$str);
	
	return($str);
}

/**********************************************************/
/*                                                        */
/* Fonction de retour la signature pour les notifications */
/*                                                        */
/**********************************************************/

function wats_get_mail_notification_signature()
{
	global $wats_settings;

	return(esc_html(str_replace(array('\r\n','\r','<br />'),"\r\n",html_entity_decode(stripslashes($wats_settings['notification_signature'])))));
}

/***************************************************/
/*                                                 */
/* Fonction d'affichage des règles de notification */
/*                                                 */
/***************************************************/

function wats_admin_display_notification_rule($rule)
{
	global $wats_settings,$wats_rule_scope;
	
	$output = '';
	
	$listepriority = isset($wats_settings['wats_priorities']) ? $wats_settings['wats_priorities'] : 0;
	$listetype = isset($wats_settings['wats_types']) ? $wats_settings['wats_types'] : 0;
	$listestatus = isset($wats_settings['wats_statuses']) ? $wats_settings['wats_statuses'] : 0;
	$listeproduct = isset($wats_settings['wats_products']) ? $wats_settings['wats_products'] : 0;
	
	foreach ($rule AS $key => $value)
	{
		if (strlen($output) > 0 && !in_array($key,array('notify_author','notify_owner','notify_admins','notify_updaters')))
			$output .= __(' AND ','WATS');
		if ($value != "0")
		{
			switch ($key)
			{
				case "scope" : $output .= $key." : ".$wats_rule_scope[$value]; break;
				case "priority" : $output .= $key." : ".$listepriority[$value]; break;
				case "type" : $output .= $key." : ".$listetype[$value]; break;
				case "status" : $output .= $key." : ".$listestatus[$value]; break;
				case "product" : $output .= $key." : ".$listeproduct[$value]; break;
				case "country" : $output .= $key." : ".$value; break;
				case "company" : $output .= $key." : ".$value; break;
				case "category" : $output .= $key." : ".get_cat_name($value); break;
				case "duedatefield" : $output .= $key." : ".$value ; break;
				case "duedateinterval" : $output .= sprintf(__('Notification frequency : every %d days','WATS'),$value); break;
				case "duedatestart" : $output .= sprintf(__('Notification start : %d days before the due date','WATS'),$value) ; break;

				default : break;
			}
		}
		else
			$output .= $key." : ".__('Any','WATS');
	}

	return $output;
}

/****************************************************/
/*                                                  */
/* Fonction d'affichage des utilisateurs à notifier */
/*                                                  */
/****************************************************/

function wats_admin_display_notification_rules_notifiers($rule)
{
	$output = '';
	foreach ($rule AS $key => $value)
	{
		if (strlen($output) > 0 && $value == 'true')
			$output .= __(' AND ','WATS');
		switch ($key)
		{
			case "notify_author" : if ($value == 'true') $output .= __('Ticket author','WATS'); break;
			case "notify_owner" : if ($value == 'true') $output .= __('Ticket owner','WATS'); break;
			case "notify_admins" : if ($value == 'true') $output .= __('All administrators','WATS'); break;
			case "notify_updaters" : if ($value == 'true') $output .= __('Ticket updaters','WATS'); break;
			default : break;
		}
	}
	
	return $output;
}

/*******************************************************/
/*                                                     */
/* Fonction de construction des règles de notification */
/*                                                     */
/*******************************************************/

function wats_admin_build_notification_rule($rule)
{
	$rules = explode(";",$rule);
	
	unset($rules[count($rules)-1]);
	$ruleset = array();
	foreach ($rules AS $rulesentry)
	{
		$newrule = explode(":",$rulesentry);
		$ruleset = array_merge($ruleset,array($newrule[0] => $newrule[1]));
	}
	
	return $ruleset;
}

/****************************************************************/
/*                                                              */
/* Fonction de mise à jour du format des règles de notification */
/*                                                              */
/****************************************************************/

function wats_update_notification_rules_format()
{
	$wats_notification_rules = get_option('wats_notification_rules');

	if (is_array($wats_notification_rules))
	{
		foreach ($wats_notification_rules AS $key => $rulesentry)
		{
			foreach ($rulesentry as $rule => $list)
			{
				if (strstr($rule,'scope') == false)
				{
					$rule = "scope:1;".$rule;
				}
			}
			$wats_notification_rules[$key] = array($rule => $list);
		}
	
		update_option('wats_notification_rules', $wats_notification_rules);
	}
		
	return;
}

function wats_get_posts_with_shortcode($shortcode)
{
	global $wpdb;
	
	$posts = $wpdb->get_results($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_content LIKE %s AND post_status='publish'",'%'.$wpdb->esc_like($shortcode).'%'));
	
	return $posts;
}

function wats_compare_url($url1,$url2)
{
	if (parse_url($url1,PHP_URL_HOST) != parse_url($url2,PHP_URL_HOST))
		return false;
		
	return true;
}

?>