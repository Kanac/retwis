<?php
include("retwis.php");

if (!isLoggedIn() || !gt("status")) {
    header("Location:index.php");
    exit;
}

$r = redisLink();
$postid = $r->incr("next_post_id");
$status = str_replace("\n"," ",gt("status"));
$r->hmset("post:$postid","user_id",$User['id'],"time",time(),"body",$status);
$followers = $r->zrange("followers:".$User['id'],0,-1); // get the followers array 
$followers[] = $User['id']; /* Add the post to our own posts too. [] is the same as .array_push()*/ 

foreach($followers as $fid) {
    $r->lpush("posts:$fid",$postid);
}
# Push the post on the timeline, and trim the timeline to the
# newest 1000 elements. This is the global timeline to everyone.
$r->lpush("timeline",$postid);
$r->ltrim("timeline",0,1000);

header("Location: index.php");
?>
