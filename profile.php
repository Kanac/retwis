<?php
include("retwis.php");
include("header.php");

$r = redisLink();
if (!gt("u") || !($userid = $r->hget("users",gt("u")))) {
    header("Location: index.php");
    exit(1);
}
echo("<h2 class=\"username\">".utf8entities(gt("u"))."</h2>");
if (isLoggedIn() && $User['id'] != $userid) {
    $isfollowing = $r->zscore("following:".$User['id'],$userid); // check if you are following this user (check if score is 0 or nil)
    if (!$isfollowing) {
        echo("<a href=\"follow.php?uid=$userid&f=1\" class=\"button\">Follow this user</a>");
    } else {
        echo("<a href=\"follow.php?uid=$userid&f=0\" class=\"button\">Stop following</a>");
    }

    $commonFollowers = getCommonFollowers($User['id'], $userid);
    echo("<p>You have $commonFollowers common followers!</p>");

    $commonFollowing = getCommonFollowing($User['id'], $userid);
    echo("<p>You are both following $commonFollowing users!</p>");
}
?>
<?php
$start = gt("start") === false ? 0 : intval(gt("start"));
showUserPostsWithPagination(gt("u"),$userid,$start,10);
include("footer.php")
?>
