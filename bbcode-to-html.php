<?php 

error_reporting(1);

$INFO = array (
  'sql_host' => '',
  'sql_database' => '',
  'sql_user' => '',
  'sql_pass' => '',
);

$nsb = new mysqli($INFO['sql_host'], $INFO['sql_user'],$INFO['sql_pass'],$INFO['sql_database']);

function stripslashes_deep($value) {
	$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
  $value = explode("\\",$value);
	$value = implode("",$value);
	return $value;
}	


function stripBBCodeAll($String) { 
	$OpenBrace = '\['; 
	$CloseBrace = '\]'; 
	$NotACloseBrace = "[^{$CloseBrace}]"; 
	$Multiple = '+?'; 
	$Optional = '?'; 
	$Anything = "(.{$Multiple})"; 
	$StartTag = "{$OpenBrace}({$NotACloseBrace}{$Multiple})(={$NotACloseBrace}{$Multiple}){$Optional}{$CloseBrace}"; 
	$EndTag = "{$OpenBrace}/\\1{$CloseBrace}"; 
	$FullPattern = "~{$StartTag}{$Anything}{$EndTag}~"; 
	return preg_replace($FullPattern, '', $String); 
} 

function stripBBCode($text_to_search) { 
	$pattern = '|[[\/\!]*?[^\[\]]*?]|si'; 
	$replace = ' '; 
	return preg_replace($pattern, $replace, $text_to_search); 
} 

function forumbbcode($text) { 
        
	$text = htmlspecialchars_decode(html_entity_decode($text));
	//$text = strip_tags($text, '<p><span><div><img><embed><iframe><br>');

	$suche = array(	'/\[center\](.*?)\[\/center\]/is',
			'/\[snapback\](.*?)\[\/snapback\]/is',
			'/\[spoiler\](.*?)\[\/spoiler\]/is',				
			'/\[size=(.*?)\](.*?)\[\/size\]/is', 
			'/\[size="(.*?)"\](.*?)\[\/size\]/is',
			'/\[quote name=\'(.*?)\' date=\'(.*?)\'](.*?)\[\/quote\]/is', 
	        '/\[color=(.*?)\](.*?)\[\/color\]/is', 
	        '/\[font=(.*?)\](.*?)\[\/font\]/is',
	        '/\[font="(.*?)"\](.*?)\[\/font\]/is',
	        '/\[b\](.*?)\[\/b\]/is', 
	        '/\[i\](.*?)\[\/i\]/is', 
	        '/\[u\](.*?)\[\/u\]/is', 
	        '/\[s\](.*?)\[\/s\]/is', 
	        '/\[img\](.+?)\[\/img\]/i',
	        '/\[left\](.*?)\[\/left\]/is',
	        '/\[right\](.*?)\[\/right\]/is',  
	        '/\[url=(.+?)\](.+?)\[\/url\]/is',
	        '/\[url\](.+?)\[\/url\]/is',
	        '/\[quote name=(.*?)\ date=(.*?)\ timestamp=(.*?)\ post=(.*?)\](.*?)\[\/quote\]/is',
	        '/\[quote name=(.*?)\ date=(.*?)\](.*?)\[\/quote\]/is',
	        '/\[quote\](.*?)\[\/quote\]/is');



// bbcode


//[center]text[/center] //
//[snapback]text[/snapback] //
//[spoiler]text[/spoiler] //
//[size=2]text[/size]
//[size='3']text[/size]
//[quote name='user' date='12/12/1992']text[/]
//[color='blue']text[/color]
//[font='arial']text[/font]
//[b]text[/b]
//[i]text[/i]
//[u]text[/u]
//[s]text[/s]
//[img]text[/img]
//[left]text[/left]
//[right]text[/right]
//[url=http]text[/url]
//[url]http:text[/url]
//[quote name=name date=sept12 timestamp=5353 post=1337]text[/quote]
//[quote name=name date=sept11]text[/quote]

//[quote]text[/quote]



	        
	    $code = array(
			'<div style="text-align:center;"><p>$1</p></div>',
			'',
	        '<div class="ipsSpoiler" data-ipsspoiler=""><div class="ipsSpoiler_header"><span></span></div><div class="ipsSpoiler_contents"><p>$1</p></div></div>',
	        '<p>$2</p>',
	        '<p>$2</p>',
	        '<blockquote class="ipsQuote" data-ipsquote="" data-ipsquote-username="$1" data-ipsquote-timestamp=""><div class="ipsQuote_citation"></div><div class="ipsQuote_contents"><p>$3</p></div></blockquote>',
	        '<span style=color:$1>$2</span>',
	        '<span style="font-size:$1em">$2</span>',
	        '<span style="font-size:$1em">$2</span>',
	        '<strong>$1</strong>',
	        '<em>$1</em>',
	        '<span style="text-decoration:underline;">$1</span>',
	        '<span style="text-decoration:line-through;">$1</span>',
	        '<img src="$1" class="ipsImage" />',
	        '<div style="text-align:left;"><p>$1</p></div>',
	        '<div style="text-align:right;"><p>$1</p></div>',
	        '<a href=$1 rel="external nofollow">$2</a>',
	        '<a href=$1 rel="external nofollow">Link</a>',
	        '<blockquote class="ipsQuote" data-ipsquote="" data-ipsquote-username="$1" data-ipsquote-timestamp="$3"><div class="ipsQuote_contents"><p>$5</p></div></blockquote>',
	        '<blockquote class="ipsQuote" data-ipsquote="" data-ipsquote-username="$1"><div class="ipsQuote_contents"><p>$3<p></div></blockquote>',
	        '<blockquote class="ipsQuote" data-ipsquote="" data-ipsquote-username="$1" data-ipsquote-timestamp="$2"><div class="ipsQuote_contents"><p>$3</p></div></blockquote>'
	                      );
	        
		//$text = str_replace('http://', ' http://', $text);    // left single quote

	  $text = stripslashes($text); 
	  $text = preg_replace($suche, $code, $text);
// $text = preg_replace('%(((f|ht){1}tp://)[-a-zA-^Z0-9@:\%_\+.~#?&//=]+)%i','<a href="\\1">\\1</a>', $text);

		//$text = preg_replace('/(?<!http:\/\/|https:\/\/|\"|=|\'|\'>|\">)(www\..*?)(\s|\Z|\.\Z|\.\s|\<|\>|,)/i',"<a target=\"blank\" rel=\"nofollow\" href=\"http://$1\">$1</a>$2",$text);
		//$text = preg_replace('/(?<!\"|=|\'|\'>|\">|site:)(https?:\/\/(www){0,1}.*?)(\s|\Z|\.\Z|\.\s|\<|\>|,)/i',"<a target=\"blank\" rel=\"nofollow\" href=\"$1\">$1</a>$3",$text);

 //$text= preg_replace("/(^|[\n ])([\w]*?)((ht|f)tp(s)?:\/\/[\w]+[^ \,\"\n\r\t<]*)/is", "$1$2<a href=\"$3\" target=\"_blank\" rel=\"nofollow\">$3</a>", $text);  
 //   $text= preg_replace("/(^|[\n ])([\w]*?)((www|ftp)\.[^ \,\"\t\n\r<]*)/is", "$1$2<a href=\"http://$3\" target=\"_blank\" rel=\"nofollow\" >$3</a>", $text);  
   // $text= preg_replace("/(^|[\n ])([a-z0-9&\-_\.]+?)@([\w\-]+\.([\w\-\.]+)+)/i", "$1<a href=\"mailto:$2@$3\">$2@$3</a>", $text);  

	    	// here
		//$text = $text . "<!--$rawtext-->";

		//$text = stripBBCode($text);
	
	    return $text;
	}



function has_links ($text) { 

		$text = preg_replace('/(?<!http:\/\/|https:\/\/|\"|=|\'|\'>|\">)(www\..*?)(\s|\Z|\.\Z|\.\s|\<|\>|,)/i',"<a target=\"blank\" rel=\"nofollow\" href=\"http://$1\">$1</a>$2",$text);
		$text = preg_replace('/(?<!\"|=|\'|\'>|\">|site:)(https?:\/\/(www){0,1}.*?)(\s|\Z|\.\Z|\.\s|\<|\>|,)/i',"<a target=\"blank\" rel=\"nofollow\" href=\"$1\">$1</a>$3",$text);

		$filtered_content = $text;
		// ebay rover links
		$pieces = explode("rover.", $filtered_content);
	
		if ($pieces[1]) { } else {
		$filtered_content = preg_replace("/(http:\/\/www.ebay.[^\s]+)/", "http://rover.ebay.com/rover/1/711-53200-19255-0/1?campid=5336772688&amp;pub=5574945724&amp;customid=&amp;toolid=10004&amp;mpre=$1", $filtered_content);
		$filtered_content = preg_replace("/(http:\/\/m.ebay.[^\s]+)/", "http://rover.ebay.com/rover/1/711-53200-19255-0/1?campid=5336772688&amp;pub=5574945724&amp;customid=&amp;toolid=10004&amp;mpre=$1", $filtered_content);		
		$filtered_content = preg_replace("/(http:\/\/cgi.ebay.co[^\s]+)/", "http://rover.ebay.com/rover/1/711-53200-19255-0/1?campid=5336772688&amp;pub=5574945724&amp;customid=&amp;toolid=10004&amp;mpre=$1", $filtered_content);
		$filtered_content = preg_replace("/(http:\/\/myworld.ebay.co[^\s]+)/", "http://rover.ebay.com/rover/1/711-53200-19255-0/1?campid=5336772688&amp;pub=5574945724&amp;customid=&amp;toolid=10004&amp;mpre=$1", $filtered_content);
		$filtered_content = preg_replace("/(http:\/\/item.mobileweb.ebay.co[^\s]+)/", "http://rover.ebay.com/rover/1/711-53200-19255-0/1?campid=5336772688&amp;pub=5574945724&amp;customid=&amp;toolid=10004&amp;mpre=$1", $filtered_content);
		$filtered_content = preg_replace("/(http:\/\/feedback.ebay.co[^\s]+)/", "http://rover.ebay.com/rover/1/711-53200-19255-0/1?campid=5336772688&amp;pub=5574945724&amp;customid=&amp;toolid=10004&amp;mpre=$1", $filtered_content);
		$filtered_content = preg_replace("/(http:\/\/shop.ebay.co[^\s]+)/", "http://rover.ebay.com/rover/1/711-53200-19255-0/1?campid=5336772688&amp;pub=5574945724&amp;customid=&amp;toolid=10004&amp;mpre=$1", $filtered_content);
		$filtered_content = preg_replace("/(http:\/\/pages.ebay.co[^\s]+)/", "http://rover.ebay.com/rover/1/711-53200-19255-0/1?campid=5336772688&amp;pub=5574945724&amp;customid=&amp;toolid=10004&amp;mpre=$1", $filtered_content);
		}

		// nikestore linkshare
		$pieces = explode("nike.", $filtered_content);
	
		if ($pieces[1]) { 
		$filtered_content = preg_replace("/(http:\/\/store.nike.[^\s]+)/", "http://click.linksynergy.com/fs-bin/click?id=VUpgj5SICOg&subid=&offerid=300375.1&type=10&tmpid=12957&RD_PARM1=$1", $filtered_content);
		}
	
		return $filtered_content;
}



// convert the old BBCode to HTML

$stmt = $nsb->prepare("SELECT * FROM forums_posts WHERE pid > 0 ORDER BY pid LIMIT 100000");
$stmt->execute();
$result = $stmt->get_result();
while ($data = $result->fetch_object()) { 
		echo  $data->pid ." ";
		// backup post

		$post_cleanse = '';
	    $post_cleanse = stripslashes_deep(has_links(forumbbcode($data->post)));

		$st = $nsb->prepare("INSERT INTO old_post (`post_id`, `og_post_bbcode`, `modded_bbcode`) VALUES (?,?,?)");
	    if ( false===$st ) {
	      die('prepare() failed: ' . htmlspecialchars($nsb->error));
	    }
	    $rc = $st->bind_param("iss", $data->pid, $data->post, $post_cleanse);
	    if ( false===$rc ) {
	      die('bind_param() failed: ' . htmlspecialchars($st->error));
	    }
	    $rc = $st->execute();
	    if ( false===$rc ) {
	      die('execute() failed: ' . htmlspecialchars($st->error));
	    }
	    $st->close();





		// insert posts

	  $sup = $nsb->prepare("UPDATE `forums_posts` SET `post` = ? WHERE `pid` = ?");

	  $sup->bind_param('si', $post_cleanse, $data->pid);

		$sup->execute();
    $sup->close();
        

	}
	$stmt->close();
