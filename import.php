<?php 

//exit();

$importAvatar = '';
$importUsers = '';
$importForums = '';
$importPosts = '';
$importTopics = '';
$updateTopics = '1';

	function stripslashes_deep($value) {
		$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
        	$value = explode("\\",$value);
	        $value = implode("",$value);
		return $value;
	}	


// DB connect
$sbdb = new mysqli('$db_server', '$db_user','$db_pass','old_db');
$nsb = new mysqli('$db_server', '$db_user','$db_pass','new_db');



// Import Avatars
//------------------

if ($importAvatar) { 
	$stmt = $sbdb->prepare("SELECT pp_member_id, avatar_location FROM ibf_profile_portal WHERE pp_member_id != 1 ORDER BY pp_member_id ASC");
	$stmt->execute();
	$result = $stmt->get_result();

	while ($data = $result->fetch_object()) { 

		echo  $data->pp_member_id ." ";
	    $nsb->query("UPDATE core_members SET pp_main_photo = '$data->avatar_location', pp_thumb_photo = '$data->avatar_location' WHERE member_id = $data->pp_member_id");

	}

	$stmt->close();
}



// Import Avatars
//------------------

if ($updateTopics) { 
	$stmt = $nsb->prepare("SELECT tid FROM forums_topics WHERE topic_firstpost = 0 ORDER BY tid ASC");
	$stmt->execute();
	$result = $stmt->get_result();

	while ($data = $result->fetch_object()) { 

		echo  $data->tid ." "; $firstpost = '0';
		$firstpost = $nsb->query("SELECT pid FROM forums_posts WHERE topic_id = $data->tid ORDER BY pid ASC LIMIT 1")->fetch_object()->pid;
	    $nsb->query("UPDATE forums_topics SET topic_firstpost = '$firstpost' WHERE tid = $data->tid");

	}

	$stmt->close();
}














// Migrate Members
//------------------

if ($importUsers) { 
	$stmt = $sbdb->prepare("SELECT * FROM ibf_members JOIN ibf_profile_portal ON pp_member_id = member_id WHERE member_id NOT IN (1) AND email != '' AND member_banned = '' GROUP BY email ORDER BY member_id ASC");
	$stmt->execute();
	$result = $stmt->get_result();

	while ($data = $result->fetch_object()) { 

		echo  $data->member_id ." ";
	    $st = $nsb->prepare("INSERT INTO core_members (`member_id`,
			`name`,
			`member_group_id`,	
			`email`,
			`joined`,
			`ip_address`,	
			`warn_level`,
			`warn_lastwarn`,	
			`language`,	
			`bday_day`,
			`bday_month`,	
			`bday_year`,
			`msg_count_new`,	
			`msg_count_total`,	
			`last_visit`,
			`last_activity`,
			`member_login_key`,	
			`member_login_key_expire`,	
			`members_seo_name`,	
			`members_profile_views`,	
			`members_pass_hash`,
			`members_pass_salt`,	
			`fb_uid`,
			`twitter_id`,	
			`twitter_token`,	
			`twitter_secret`,	
			`fb_token`,
			`pp_main_photo`,
			`pp_main_width`,
			`pp_main_height`,	
			`pp_thumb_photo`,
			`pp_thumb_width`,
			`pp_thumb_height`,
			`pp_setting_count_comments`,
			`signature`,
			`member_title`,	
			`member_posts`,	
			`member_last_post`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
	    if ( false===$st ) {
	      die('prepare() failed: ' . htmlspecialchars($nsb->error));
	    }

	    $rc = $st->bind_param("ssssssssssssssssssssssssssssssssssssss", $data->member_id, 
			 html_entity_decode($data->name),	
			$data->member_group_id, 	
			$data->email,
			$data->joined,	
			$data->ip_address,	
			$data->warn_level,	
			$data->warn_lastwarn,	
			$data->language,
			$data->bday_day,	
			$data->bday_month,	
			$data->bday_year,
			$data->msg_count_new,	
			$data->msg_count_total,	
			$data->last_visit,
			$data->last_activity,
			$data->member_login_key,	
			$data->member_login_key_expire,	
			$data->members_seo_name,	
			$data->members_profile_views,
			$data->members_pass_hash,
			$data->members_pass_salt,
			$data->fb_uid,
			$data->twitter_id,	
			$data->twitter_token,	
			$data->twitter_secret,
			$data->fb_token,
			$data->avatar_location,
			$data->pp_main_width,
			$data->pp_main_height,	
			$data->pp_thumb_photo,	
			$data->pp_thumb_width,	
			$data->pp_thumb_height,		
			$data->pp_setting_count_comments,
			 html_entity_decode($data->signature),
			$post->title,
			$data->posts,	
			$data->last_post);
	    if ( false===$rc ) {
	      die('bind_param() failed: ' . htmlspecialchars($st->error));
	    }
	    $rc = $st->execute();
	    if ( false===$rc ) {
	      die('execute() failed: ' . htmlspecialchars($st->error));
	    }
	    $st->close();

	}

	$stmt->close();
}


// Migrate Forums
//------------------

if ($importForums) { 
	$stmt = $sbdb->prepare("SELECT * FROM ibf_forums ORDER BY id");
	$stmt->execute();
	$result = $stmt->get_result();



	while ($data = $result->fetch_object()) { 


		echo  $data->id ." ";
		// insert forums
		$word = 'forums_forum_'.$data->id;
		$desc = 'forums_forum_'.$data->id.'_desc';
		$ruti = 'forums_forum_'.$data->id.'_rulestitle';
		$ru = 'forums_forum_'.$data->id.'_rules';
		$erm = 'forums_forum_'.$data->id.'_permerror';

		$nsb->query("insert into `core_sys_lang_words` (`lang_id`, `word_app`, `word_key`, `word_default`, `word_custom`) VALUES ('1', 'forums', '$word', '$data->name', '$data->name')");

		if ($data->description) { 
			$nsb->query("insert into core_sys_lang_words (lang_id, word_app, word_key, word_default, word_custom) VALUES ('1', 'forums', '$desc', '$data->description', '$data->description')"); 
		} else { 
			$nsb->query("insert into core_sys_lang_words (lang_id, word_app, word_key) VALUES ('1', 'forums', '$desc')");
		}
		$nsb->query("insert into core_sys_lang_words (lang_id, word_app, word_key) VALUES ('1', 'forums', '$ruti')");
		$nsb->query("insert into core_sys_lang_words (lang_id, word_app, word_key) VALUES ('1', 'forums', '$ru')");
		$nsb->query("insert into core_sys_lang_words (lang_id, word_app, word_key) VALUES ('1', 'forums', '$erm')");





	    $st = $nsb->prepare("INSERT INTO forums_forums (`id`, `topics`, `posts`, `last_post`, `last_poster_id`, `last_poster_name`, `position`, `last_title`, `last_id`, `parent_id`, `name_seo`,`seo_last_title`, password_override, sort_key, show_rules, preview_posts, ipseo_priority, qa_rate_questions, qa_rate_answers, skin_id, forums_bitoptions) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,'','last_post','0','0','-1', '*','*', '0', '8')");
	    if ( false===$st ) {
	      die('prepare() failed: ' . htmlspecialchars($nsb->error));
	    }

	    $rc = $st->bind_param("ssssssssssss", $data->id, 
			$data->topics,	
			$data->posts, 	
			$data->last_post,
			$data->last_poster_id,	
			$data->last_name,	
			$data->position,	
			$data->last_title,	
			$data->last_id,	
			$data->parent_id,	
			$data->name_seo,	
			$data->seo_last_title);
	    if ( false===$rc ) {
	      die('bind_param() failed: ' . htmlspecialchars($st->error));
	    }
	    $rc = $st->execute();
	    if ( false===$rc ) {
	      die('execute() failed: ' . htmlspecialchars($st->error));
	    }
	    $st->close();

	}

	$stmt->close();
}

// Migrate Posts
//------------------

if ($importPosts) { 
	$stmt = $sbdb->prepare("SELECT * FROM ibf_posts WHERE pid > 2067038 ORDER BY pid");
	$stmt->execute();
	$result = $stmt->get_result();




	while ($data = $result->fetch_object()) { 


		echo  $data->pid ." ";
		// insert posts

	    $st = $nsb->prepare("INSERT INTO forums_posts (`pid`, `edit_time`, `author_id`, `author_name`, `ip_address`, `post_date`, `post`, `queued`, `topic_id`, `edit_name`, `post_edit_reason`	
) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
	    if ( false===$st ) {
	      die('prepare() failed: ' . htmlspecialchars($nsb->error));
	    }

	    $rc = $st->bind_param("sssssssssss", $data->pid,
	    	$data->edit_time,
	    	$data->author_id,
	    	$data->name,
	    	$data->ip_address,
	    	$data->post_date,
	    	stripslashes_deep(html_entity_decode($data->post)),
	    	$data->queued,
	    	$data->topic_id,
	    	$data->edit_name,
	    	$data->post_edit_reason);
	    if ( false===$rc ) {
	      die('bind_param() failed: ' . htmlspecialchars($st->error));
	    }
	    $rc = $st->execute();
	    if ( false===$rc ) {
	      die('execute() failed: ' . htmlspecialchars($st->error));
	    }
	    $st->close();

	}

	$stmt->close();
}




























// Migrate Topics
//------------------

if ($importTopics) { 
	$stmt = $sbdb->prepare("SELECT * FROM ibf_topics ORDER BY tid");
	$stmt->execute();
	$result = $stmt->get_result();

	while ($data = $result->fetch_object()) { 


		echo  $data->tid ." ";
		// insert topics

		$last_poster_name = '';

		$last_poster_name = $nsb->query("SELECT name FROM core_members WHERE member_id=$data->last_poster_id LIMIT 1")->fetch_object()->name;

		if (!$last_poster_name) $last_poster_name = '';

	    $st = $nsb->prepare("INSERT INTO forums_topics (`tid`, `title`, `state`, `posts`, `starter_id`, `start_date`,`last_poster_id`, `last_post`, `last_poster_name`, `views`, `forum_id`, `approved`, `pinned`,`topic_open_time`, `topic_close_time`,`topic_rating_hits`,`title_seo`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
	    if ( false===$st ) {
	      die('prepare() failed: ' . htmlspecialchars($nsb->error));
	    }

	    $rc = $st->bind_param("sssssssssssssssss", $data->tid,
	    	$data->title,
	    	$data->state,
	    	$data->posts,
	    	$data->starter_id,
	    	$data->created,
	    	$data->last_poster_id,
	    	$data->last_post,
	    	$last_poster_name,
	    	$data->views,
	    	$data->forum_id,
	    	$data->approved,
	    	$data->pinned,
	    	$data->topic_open_time,
	    	$data->topic_close_time,
	    	$data->topic_rating_hits,
	    	$data->title_seo);
	    if ( false===$rc ) {
	      die('bind_param() failed: ' . htmlspecialchars($st->error));
	    }
	    $rc = $st->execute();
	    if ( false===$rc ) {
	      die('execute() failed: ' . htmlspecialchars($st->error));
	    }
	    $st->close();

	}

	$stmt->close();
}

