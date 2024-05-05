<?php
/**
 * Country Flag
 * Copyright 2013 Starpaul20
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$page->add_breadcrumb_item($lang->countries, "index.php?module=config-countries");

$lang->load("country", true);

if($mybb->input['action'] == "add")
{
	if($mybb->request_method == "post")
	{
		if(!trim($mybb->input['name']))
		{
			$errors[] = $lang->error_missing_name;
		}

		if(!trim($mybb->input['flag']))
		{
			$errors[] = $lang->error_missing_flag;
		}

		if(!$errors)
		{
			$new_country = array(
				'name' => $db->escape_string($mybb->input['name']),
				'flag' => $db->escape_string($mybb->input['flag'])
			);

			$cid = $db->insert_query("countries", $new_country);

			update_countries();

			// Log admin action
			$name = $lang->parse($mybb->input['name']);
			log_admin_action($cid, htmlspecialchars_uni($name));

			flash_message($lang->success_country_added, 'success');
			admin_redirect('index.php?module=config-countries');
		}
	}

	$page->add_breadcrumb_item($lang->add_country);
	$page->output_header($lang->countries." - ".$lang->add_country);

	$sub_tabs['manage_countries'] = array(
		'title'	=> $lang->manage_countries,
		'link' => "index.php?module=config-countries"
	);

	$sub_tabs['add_country'] = array(
		'title'	=> $lang->add_country,
		'link' => "index.php?module=config-countries&amp;action=add",
		'description' => $lang->add_country_desc
	);

	$sub_tabs['add_multiple'] = array(
		'title' => $lang->add_multiple_countries,
		'link' => "index.php?module=config-countries&amp;action=add_multiple"
	);

	$page->output_nav_tabs($sub_tabs, 'add_country');

	if($errors)
	{
		$page->output_inline_error($errors);
	}
	else
	{
		$mybb->input['flag'] = 'images/flags';
	}

	$form = new Form("index.php?module=config-countries&amp;action=add", "post", "add");
	$form_container = new FormContainer($lang->add_country);
	$form_container->output_row($lang->name." <em>*</em>", htmlspecialchars_uni($lang->country_name_desc), $form->generate_text_box('name', $mybb->get_input('name'), array('id' => 'name')), 'name');
	$form_container->output_row($lang->flag_path." <em>*</em>", $lang->flag_path_desc, $form->generate_text_box('flag', $mybb->get_input('flag'), array('id' => 'flag')), 'flag');
	$form_container->end();

	$buttons[] = $form->generate_submit_button($lang->save_country);

	$form->output_submit_wrapper($buttons);

	$form->end();

	$page->output_footer();
}

if($mybb->input['action'] == "add_multiple")
{
	if($mybb->request_method == "post")
	{
		if($mybb->input['step'] == 1)
		{
			if(!trim($mybb->input['pathfolder']))
			{
				$errors[] = $lang->error_missing_path_multiple;
			}

			$path = $mybb->input['pathfolder'];
			$dir = @opendir(MYBB_ROOT.$path);
			if(!$dir)
			{
				$errors[] = $lang->error_invalid_path;
			}

			if(substr($path, -1, 1) !== "/")
			{
				$path .= "/";
			}

			$query = $db->simple_select("countries");

			$aflags = array();
			while($flag = $db->fetch_array($query))
			{
				$aflags[$flag['flag']] = 1;
			}

			$flags = array();
			if(!$errors)
			{
				while($file = readdir($dir))
				{
					if($file != ".." && $file != ".")
					{
						$ext = get_extension($file);
						if($ext == "gif" || $ext == "jpg" || $ext == "jpeg" || $ext == "png" || $ext == "bmp")
						{
							if(!isset($aflags[$path.$file]))
							{
								$flags[] = $file;
							}
						}
					}
				}
				closedir($dir);

				if(count($flags) == 0)
				{
					$errors[] = $lang->error_no_flag_images;
				}
			}

			// Check for errors again (from above statement)!
			if(!$errors)
			{
				// We have no errors so let's proceed!
				$page->add_breadcrumb_item($lang->add_multiple_countries);
				$page->output_header($lang->countries." - ".$lang->add_multiple_countries);

				$sub_tabs['manage_countries'] = array(
					'title'	=> $lang->manage_countries,
					'link' => "index.php?module=config-countries"
				);

				$sub_tabs['add_country'] = array(
					'title'	=> $lang->add_country,
					'link' => "index.php?module=config-countries&amp;action=add"
				);

				$sub_tabs['add_multiple'] = array(
					'title' => $lang->add_multiple_countries,
					'link' => "index.php?module=config-countries&amp;action=add_multiple",
					'description' => $lang->add_multiple_countries_desc
				);

				$page->output_nav_tabs($sub_tabs, 'add_multiple');

				$form = new Form("index.php?module=config-countries&amp;action=add_multiple", "post", "add_multiple");
				echo $form->generate_hidden_field("step", "2");
				echo $form->generate_hidden_field("pathfolder", $path);

				$form_container = new FormContainer($lang->add_multiple_countries);
				$form_container->output_row_header($lang->flag, array("class" => "align_center", 'width' => '10%'));
				$form_container->output_row_header($lang->name);
				$form_container->output_row_header($lang->add, array("class" => "align_center", 'width' => '5%'));

				foreach($flags as $key => $file)
				{
					$ext = get_extension($file);
					$find = str_replace(".".$ext, "", $file);
					$name = ucfirst($find);

					$form_container->output_cell("<img src=\"../".$path.$file."\" alt=\"\" /><br /><small>{$file}</small>", array("class" => "align_center", "width" => 1));
					$form_container->output_cell($form->generate_text_box("name[{$file}]", $name, array('id' => 'name', 'style' => 'width: 98%')));
					$form_container->output_cell($form->generate_check_box("include[{$file}]", 1, "", array('checked' => 1)), array("class" => "align_center"));
					$form_container->construct_row();
				}

				if($form_container->num_rows() == 0)
				{
					flash_message($lang->error_no_flag_images, 'error');
					admin_redirect("index.php?module=config-countries&action=add_multiple");
				}

				$form_container->end();

				$buttons[] = $form->generate_submit_button($lang->save_countries);
				$form->output_submit_wrapper($buttons);

				$form->end();

				$page->output_footer();
				exit;
			}
		}
		else
		{
			$path = $mybb->input['pathfolder'];
			reset($mybb->input['include']);
			$name = $mybb->input['name'];

			if(empty($mybb->input['include']))
			{
				flash_message($lang->error_none_included_flags, 'error');
				admin_redirect("index.php?module=config-countries&action=add_multiple");
			}

			foreach($mybb->input['include'] as $image => $insert)
			{
				if($insert)
				{
					$new_country = array(
						'name' => $db->escape_string($name[$image]),
						'flag' => $db->escape_string($path.$image)
					);

					$db->insert_query("countries", $new_country);
				}
			}

			update_countries();

			// Log admin action
			log_admin_action();

			flash_message($lang->success_countries_added, 'success');
			admin_redirect("index.php?module=config-countries");
		}
	}

	$page->add_breadcrumb_item($lang->add_multiple_countries);
	$page->output_header($lang->countries." - ".$lang->add_multiple_countries);

	$sub_tabs['manage_countries'] = array(
		'title'	=> $lang->manage_countries,
		'link'	=> "index.php?module=config-countries"
	);

	$sub_tabs['add_country'] = array(
		'title'	=> $lang->add_country,
		'link'	=> "index.php?module=config-countries&amp;action=add"
	);

	$sub_tabs['add_multiple'] = array(
		'title' => $lang->add_multiple_countries,
		'link' => "index.php?module=config-countries&amp;action=add_multiple",
		'description'	=> $lang->add_multiple_countries_desc
	);

	$page->output_nav_tabs($sub_tabs, 'add_multiple');

	$form = new Form("index.php?module=config-countries&amp;action=add_multiple", "post", "add_multiple");
	echo $form->generate_hidden_field("step", "1");

	if($errors)
	{
		$page->output_inline_error($errors);
	}

	$form_container = new FormContainer($lang->add_multiple_countries);
	$form_container->output_row($lang->path_to_flags." <em>*</em>", $lang->path_to_flags_desc, $form->generate_text_box('pathfolder', $mybb->get_input('pathfolder'), array('id' => 'pathfolder')), 'pathfolder');
	$form_container->end();

	$buttons[] = $form->generate_submit_button($lang->show_countries);

	$form->output_submit_wrapper($buttons);
	$form->end();

	$page->output_footer();
}

if($mybb->input['action'] == "edit")
{
	$query = $db->simple_select("countries", "*", "cid='".$mybb->get_input('cid', MyBB::INPUT_INT)."'");
	$country = $db->fetch_array($query);

	if(!$country['cid'])
	{
		flash_message($lang->error_invalid_country, 'error');
		admin_redirect("index.php?module=config-countries");
	}

	if($mybb->request_method == "post")
	{
		if(!trim($mybb->input['name']))
		{
			$errors[] = $lang->error_missing_name;
		}

		if(!trim($mybb->input['flag']))
		{
			$errors[] = $lang->error_missing_flag;
		}

		if(!$errors)
		{
			$update_country = array(
				'name'	=> $db->escape_string($mybb->input['name']),
				'flag'	=> $db->escape_string($mybb->input['flag'])
			);

			$db->update_query("countries", $update_country, "cid='{$country['cid']}'");

			update_countries();

			// Log admin action
			$name = $lang->parse($mybb->input['name']);
			log_admin_action($country['cid'], htmlspecialchars_uni($name));

			flash_message($lang->success_country_updated, 'success');
			admin_redirect('index.php?module=config-countries');
		}
	}

	$page->add_breadcrumb_item($lang->edit_country);
	$page->output_header($lang->countries." - ".$lang->edit_country);

	$sub_tabs['edit_country'] = array(
		'title'	=> $lang->edit_country,
		'link'	=> "index.php?module=config-countries",
		'description'	=> $lang->edit_country_desc
	);

	$page->output_nav_tabs($sub_tabs, 'edit_country');

	$form = new Form("index.php?module=config-countries&amp;action=edit", "post", "edit");
	echo $form->generate_hidden_field("cid", $country['cid']);

	if($errors)
	{
		$page->output_inline_error($errors);
	}
	else
	{
		$mybb->input = $country;
	}

	$form_container = new FormContainer($lang->edit_country);
	$form_container->output_row($lang->name." <em>*</em>", htmlspecialchars_uni($lang->country_name_desc), $form->generate_text_box('name', $mybb->input['name'], array('id' => 'name')), 'name');
	$form_container->output_row($lang->flag_path." <em>*</em>", $lang->flag_path_desc, $form->generate_text_box('flag', $mybb->input['flag'], array('id' => 'flag')), 'flag');
	$form_container->end();

	$buttons[] = $form->generate_submit_button($lang->save_country);

	$form->output_submit_wrapper($buttons);
	$form->end();

	$page->output_footer();
}

if($mybb->input['action'] == "delete")
{
	$query = $db->simple_select("countries", "*", "cid='".$mybb->get_input('cid', MyBB::INPUT_INT)."'");
	$country = $db->fetch_array($query);

	if(!$country['cid'])
	{
		flash_message($lang->error_invalid_country, 'error');
		admin_redirect("index.php?module=config-countries");
	}

	// User clicked no
	if($mybb->get_input('no'))
	{
		admin_redirect("index.php?module=config-countries");
	}

	if($mybb->request_method == "post")
	{
		$updated_user = array(
			"country" => 0
		);

		$db->update_query("users", $updated_user, "country='{$country['cid']}'");
		$db->delete_query("countries", "cid='{$country['cid']}'");

		update_countries();

		// Log admin action
		$name = $lang->parse($country['name']);
		log_admin_action($country['cid'], htmlspecialchars_uni($name));

		flash_message($lang->success_country_deleted, 'success');
		admin_redirect("index.php?module=config-countries");
	}
	else
	{
		$page->output_confirm_action("index.php?module=config-countries&amp;action=delete&amp;cid={$country['cid']}", $lang->confirm_country_deletion);
	}
}

if(!$mybb->input['action'])
{
	$page->output_header($lang->countries);

	$sub_tabs['manage_countries'] = array(
		'title'	=> $lang->manage_countries,
		'link' => "index.php?module=config-countries",
		'description' => $lang->manage_countries_desc
	);

	$sub_tabs['add_country'] = array(
		'title'	=> $lang->add_country,
		'link' => "index.php?module=config-countries&amp;action=add"
	);

	$sub_tabs['add_multiple'] = array(
		'title' => $lang->add_multiple_countries,
		'link' => "index.php?module=config-countries&amp;action=add_multiple"
	);

	$page->output_nav_tabs($sub_tabs, 'manage_countries');

	$pagenum = $mybb->get_input('page', MyBB::INPUT_INT);
	if($pagenum)
	{
		$start = ($pagenum - 1) * 20;
	}
	else
	{
		$start = 0;
		$pagenum = 1;
	}

	$table = new Table;
	$table->construct_header($lang->flag, array('class' => "align_center", 'width' => 1));
	$table->construct_header($lang->name, array('width' => "70%"));
	$table->construct_header($lang->controls, array('class' => "align_center", 'colspan' => 2));

	$query = $db->simple_select("countries", "*", "", array('limit_start' => $start, 'limit' => 20, 'order_by' => 'name'));
	while($country = $db->fetch_array($query))
	{
		if(my_validate_url($country['flag'], true))
		{
			$image = $country['flag'];
		}
		else
		{
			$image = "../".$country['flag'];
		}

		$country['name'] = $lang->parse($country['name']);

		$table->construct_cell("<img src=\"{$image}\" alt=\"\" />", array("class" => "align_center"));
		$table->construct_cell(htmlspecialchars_uni($country['name']));

		$table->construct_cell("<a href=\"index.php?module=config-countries&amp;action=edit&amp;cid={$country['cid']}\">{$lang->edit}</a>", array("class" => "align_center"));
		$table->construct_cell("<a href=\"index.php?module=config-countries&amp;action=delete&amp;cid={$country['cid']}&amp;my_post_key={$mybb->post_code}\" onclick=\"return AdminCP.deleteConfirmation(this, '{$lang->confirm_country_deletion}')\">{$lang->delete}</a>", array("class" => "align_center"));
		$table->construct_row();
	}

	if($table->num_rows() == 0)
	{
		$table->construct_cell($lang->no_countries, array('colspan' => 4));
		$table->construct_row();
	}

	$table->output($lang->manage_countries);

	$query = $db->simple_select("countries", "COUNT(cid) AS countries");
	$total_rows = $db->fetch_field($query, "countries");

	echo "<br />".draw_admin_pagination($pagenum, "20", $total_rows, "index.php?module=config-countries&amp;page={page}");

	$page->output_footer();
}
