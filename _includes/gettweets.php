<?php

	$settings = array(
		'consumer_key' => "a4C3pOp8Ilwr6qhbwOokmM9uH",
		'consumer_secret' => "lca7vtHqTGx5gDlox5iIwzA1jPvFIeMI8ngz3kBECxlk2t6vrN",
		'oauth_access_token' => "155377095-ugciQgxkwjwHtXEWnZjFqLY4Rhxt3BFEKA20ZqTn",
		'oauth_access_token_secret' => "1uTQko3MJMKpe6h0vjblUVYldofmNNncEU5W1YWeygPje"
	);
	
	$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
	$twitterUsername = "salesforce";
	$tweetCount = 10;

	require_once('main/oauth-to-api.php');
	
	
	$getfield = '?screen_name=' . $twitterUsername . '&count=' . $tweetCount;
	$twitter = new TwitterAPITimeline($settings);
	
	$json = $twitter->setGetfield($getfield)	
				  	->buildOauth($url, $requestMethod)
				 	->performRequest();
				 			
	$twitter_data = json_decode($json, true);	
		
	function timeAgo($dateStr) {
		$timestamp = date_default_timezone_set('America/Los_Angeles');	 
		$day = 60 * 60 * 24;
		$today = time(); 
		$since = $today - $timestamp;
		 
		 if (($since / $day) < 1) {
		 
		 	$timeUnits = array(
				   array(60 * 60, 'h'),
				   array(60, 'm'),
				   array(1, 's')
			  );
			  
			  for ($i = 0, $n = count($timeUnits); $i < $n; $i++) { 
				   $seconds = $timeUnits[$i][0];
				   $unit = $timeUnits[$i][1];
			 
				   if (($count = floor($since / $seconds)) != 0) {
					   break;
				   }
			  }
		 
			  return "$count{$unit}";
			  
		 } else {
			  return date('j M', strtotime($dateStr));
		 }	 
	}
	
	function formatTweet($tweet) {
		$linkified = '@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@';
		$hashified = '/(^|[\n\s])#([^\s"\t\n\r<:]*)/is';
		$mentionified = '/(^|[\n\s])@([^\s"\t\n\r<:]*)/is';
		
		$prettyTweet = preg_replace(
			array(
				$linkified,
				$hashified,
				$mentionified
			), 
			array(
				'<a href="$1" class="link-tweet" target="_blank">$1</a>',
				'$1<a class="link-hashtag" href="https://twitter.com/search?q=%23$2&src=hash" target="_blank">#$2</a>',
				'$1<a class="link-mention" href="http://twitter.com/$2" target="_blank">@$2</a>'
			), 
			$tweet
		);
		
		return $prettyTweet;
	}
	
//--------------------------------------------------------------  HTML output
	
	echo '<ul id="tweet-list" class="tweet-list">';
	
	foreach ($twitter_data as $tweet) {
	
		$retweet = $tweet['retweeted_status'];
		$isRetweet = !empty($retweet);
		
		$retweetingUser = $isRetweet ? $tweet['user']['name'] : null;
		$retweetingUserScreenName = $isRetweet ? $tweet['user']['screen_name'] : null;
		
		$user = !$isRetweet ? $tweet['user'] : $retweet['user'];	
		$userName = $user['name'];
		$userScreenName = $user['screen_name'];
		$userAvatarURL = stripcslashes($user['profile_image_url']);
		$userAccountURL = 'http://twitter.com/' . $userScreenName;
		
		$id = $tweet['id'];
		$formattedTweet = !$isRetweet ? formatTweet($tweet['text']) : formatTweet($retweet['text']);
		$statusURL = 'http://twitter.com/' . $userScreenName . '/status/' . $id;
		$date = timeAgo($tweet['created_at']);
		
		$replyID = $tweet['in_reply_to_status_id'];
		$isReply = !empty($replyID);

		$replyURL = 'https://twitter.com/intent/tweet?in_reply_to=' . $id;
		$retweetURL = 'https://twitter.com/intent/retweet?tweet_id=' . $id;
		$favoriteURL = 'https://twitter.com/intent/favorite?tweet_id=' . $id;	
?>
				
		<li id="<?php echo 'tweetid-' . $id; ?>" class="tweet<?php 
				if ($isRetweet) echo ' is-retweet'; 
				if ($isReply) echo ' is-reply'; 
				if ($tweet['retweeted']) echo ' visitor-retweeted';
				if ($tweet['favorited']) echo ' visitor-favorited'; ?>">
			<div class="tweet-info">
				<div class="user-info">
					<a class="user-avatar-link" href="<?php echo $userAccountURL; ?>">
						<img class="user-avatar" src="<?php echo $userAvatarURL; ?>">
					</a>
					<p class="user-account">
						<a class="user-name" href="<?php echo $userAccountURL; ?>"><strong><?php echo $userName; ?></strong></a>
						<a class="user-screenName" href="<?php echo $userAccountURL; ?>">@<?php echo $userScreenName; ?></a>
					</p>
				</div>
				<a class="tweet-date permalink-status" href="<?php echo $statusURL; ?>" target="_blank">
					<?php echo $date; ?>
				</a>
			</div>
			<blockquote class="tweet-text">
				<?php 	
					echo '<p>' . $formattedTweet . '</p>'; 
				 
					echo '<p class="tweet-details">';
					
					if ($isReply) {
						echo '
							<a class="link-reply-to permalink-status" href="http://twitter.com/' . $tweet['in_reply_to_screen_name'] . '/status/' . $replyID . '">
								In reply to...
							</a>
						';
					}
					
					if ($isRetweet) {
						echo '
							<span class="retweeter">
								Retweeted by <a class="link-retweeter" href="http://twitter.com/' . $retweetingUserScreenName . '">' .
								$retweetingUser
								. '</a>
							</span>
						';
					}
					
					echo '<a class="link-details permalink-status" href="' . $statusURL . '" target="_blank">Details</a></p>';
				?>		
			</blockquote>
			<div class="tweet-actions">
				<a class="action-reply" href="<?php echo $replyURL; ?>">Reply</a>
				<a class="action-retweet" href="<?php echo $retweetURL; ?>">Retweet</a>
				<a class="action-favorite" href="<?php echo $favoriteURL; ?>">Favorite</a>
			</div>
		</li>	
			
<?php 
	}	
	
	echo '</ul>';
	
?>