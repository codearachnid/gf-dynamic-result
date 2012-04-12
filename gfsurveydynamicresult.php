<?php
/*
Plugin Name: Gravity Forms Dynamic Result
Plugin URI:
Description: Build custom result messages to display based on conditional logic.
Version: 1.0
Author: Timothy Wood (@codearachnid)
Author URI: http://www.codearachnid.com
Author Email: tim@imaginesimplicity.com
License:

Copyright 2011 Imagine Simplicity (tim@imaginesimplicity.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/
/*
add_filter("gform_pre_render_3", "gf_dynamic_confirmation_prerender");
function gf_dynamic_confirmation_prerender($form){
	foreach($form["fields"] as &$field) {
		switch($field['type']) {
			case 'html':
				if($field['cssClass'] == 'survey_result') {
					$field['type'] = 'hidden';
				}
				break;
		}
	}
	return $form;
}
*/
add_filter('gform_confirmation', 'gf_dynamic_confirmation', 10, 4);
function gf_dynamic_confirmation($confirmation, $form, $lead, $ajax){
	$Fi=1;
	$Pi=0;
	$resultAmount = null;
	$resultMsg = array("");
	foreach($form['fields'] as &$field) {
		switch($field['type']) {
			case 'radio':
				$Vi=1;
				foreach($field['choices'] as &$choice) {
					if($choice['value'] == $lead[$Fi]) {
						$Pi=$Pi+$Vi;
					}
					$Vi++;
				}
				break;
			case 'hidden':
				if($field['inputName'] == 'survey_message_levels') {
					$resultAmount = explode(",", $field['defaultValue']);
				}
				break;
			case 'html':
				if($field['cssClass'] == 'survey_result'){
					array_push($resultMsg, $field['content']);
				}
				break;
		}
		$Fi++;
	}
	//return $confirmation;
	$confirmation = "<a name='gf_{$form["id"]}' class='gform_anchor' ></a><div id='gforms_confirmation_message' class='gform_confirmation_message_{$form["id"]}'>";
	if( $resultAmount[sizeof($resultAmount) - 1] < $Pi) {
		$confirmation .= $resultMsg[sizeof($resultMsg) - 1];
	} else {
		foreach($resultAmount as $i => $needle){
			if($Pi < $needle) {
				$confirmation .=  $resultMsg[$i];
				break;
			}
		}
	} 
	return $confirmation . "</div>";
}