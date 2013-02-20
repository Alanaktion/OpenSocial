<?php
/*
// BLOCK TYPING INVALID CHARACTERS WITH JAVASCRIPT
function onKeyDownHandler(e) {
  if(!e) e=window.event;
  var code = e.keyCode || e.which;
  //do not allow any of these chars to be entered: !@#$%^&*()
  if (e.shiftKey && code >= 48 && code <= 57) {
    e.returnValue = false;
    if (e.preventDefault) {
      e.preventDefault();
    }
  }
}

// SHOW DATES AND TIMES FROM DATABASE
$datetime = date('Y-m-d H:i:s');
$date     = date('Y-m-d');
$getdate  = strtotime($dbstring);

////// DATABASE TABLES //////
https://p3nlmysqladm001.secureserver.net/nl50/425?uniqueDnsEntry=xusixsocial.db.6481928.hostedresource.com&ci=34893

mbasic {
  uid      // VARCHAR(13) - uniqid() in PHP
  email    // VARCHAR(50)
  pass     // VARCHAR(50)
  fname    // VARCHAR(30)
  lname    // VARCHAR(30)
  gender   // VARCHAR(1) - m/f
  user     // VARCHAR(50) a-zA-Z0-9_ MUST BE 5+ CHARS
  bdate    // DATE
  joindate // DATE
  joinip   // VARCHAR(11)
  country  // VARCHAR(30)
  timezone // TINYINT
}
$sql = 'CREATE TABLE `xusixsocial`.`mbasic` (`uid` VARCHAR(13) NOT NULL, `email` VARCHAR(50) NOT NULL, `pass` VARCHAR(50) NOT NULL, `fname` VARCHAR(30) NOT NULL, `lname` VARCHAR(30) NOT NULL, `gender` VARCHAR(1) NOT NULL, `user` VARCHAR(50) NOT NULL, `bdate` DATE NOT NULL, `joindate` DATE NOT NULL, `joinip` VARCHAR(11) NOT NULL, `country` VARCHAR(2) NOT NULL, `timezone` TINYINT NOT NULL) ENGINE = MyISAM';

mdetails {
  uid      // VARCHAR(13)
  state    // VARCHAR(2)
  city     // VARCHAR(24)
  relastatus // INT(1): 0=single, 1=in a relationship, 2=engaged, 3=married, 4=its complicated, 5=open relationship, 6=widowed, 7=separated, 8=divorced
  relaid   // VARCHAR(50)  %uID if is friend, otherwise store as string, allowing only a-z,A-Z," "
  phone    // VARCHAR(11)
  cell     // VARCHAR(11)
  cellprvdr // VARCHAR(2)
  aboutme  // TEXT
  music    // TEXT
  movies   // TEXT
  tvshows  // TEXT
}
$sql = 'CREATE TABLE `xusixsocial`.`mdetails` (`uid` VARCHAR(13) NOT NULL, `state` VARCHAR(2) NOT NULL, `city` VARCHAR(24) NOT NULL, `relastatus` INT(1) NOT NULL, `relaid` VARCHAR(50) NOT NULL, `phone` VARCHAR(11) NOT NULL, ADD `cell` VARCHAR(11) NOT NULL, ADD `cellpvdr` VARCHAR(2) NOT NULL, `aboutme` TEXT NOT NULL, ADD `music` TEXT NOT NULL, ADD `movies` TEXT NOT NULL, ADD `tvshows` TEXT NOT NULL) ENGINE = MyISAM';

privacy {  // All are INT(1): 0=private, 1=friends, 2=anyone
  uid      // VARCHAR(13)
  state
  city
  age
  lname
  email
  phone
  cell
}
$sql = 'CREATE TABLE `privacy` (`uid` VARCHAR(13) NOT NULL, `state` INT(1) NOT NULL, `city` INT(1) NOT NULL, `age` INT(1) NOT NULL, `lname` INT(1) NOT NULL, `email` INT(1) NOT NULL, `phone` INT(1) NOT NULL, `cell` INT(1) NOT NULL) ENGINE = MyISAM';

friends {
  uid      // VARCHAR(13)
  fid      // VARCHAR(13)
}
$sql = 'CREATE TABLE `friends` (`uid` VARCHAR(13) NOT NULL, `fid` VARCHAR(13) NOT NULL) ENGINE = MyISAM';

messages {
  from     // VARCHAR(13) uID
  to       // VARCHAR(13) uID
  text     // TEXT
  read     // INT(1)
}
$sql = 'CREATE TABLE `messages` (`from` VARCHAR(13) NOT NULL, `to` VARCHAR(13) NOT NULL, `text` TEXT NOT NULL, `read` INT(1) NOT NULL) ENGINE = MyISAM';

photos {
  uid      // VARCHAR(13)
  imgid    // VARCHAR(13) - rename uploaded file to uniqid() and thumb to uniqid()."_t"
  caption  // TEXT
}
$sql = 'CREATE TABLE `photos` (`uid` VARCHAR(13) NOT NULL, `imgid` VARCHAR(13) NOT NULL, `caption` TEXT NOT NULL) ENGINE = MyISAM';

posts {
  uid      // VARCHAR(13) - posted by
  pageid   // VARCHAR(13) - posted to (user/page)
  text     // TEXT
}
$sql = 'CREATE TABLE `posts` (`uid` VARCHAR(13) NOT NULL, `postid` VARCHAR(13) NOT NULL, `pageid` VARCHAR(13) NOT NULL, `text` TEXT NOT NULL, `datetime` DATETIME NOT NULL) ENGINE = MyISAM'; 

pages {
  uid      // VARCHAR(13) - owner/creator
  pid      // VARCHAR(13) - page ID - uniqid()
  name     // VARCHAR(60) - a-zA-Z0-9_    MUST BE 4+ CHARS
  descrip  // TEXT
}
$sql = 'CREATE TABLE `pages` (`uid` VARCHAR(13) NOT NULL, `pageid` VARCHAR(13) NOT NULL, `name` VARCHAR(60) NOT NULL, `descrip` TEXT NOT NULL) ENGINE = MyISAM';

comments {
  uid      // VARCHAR(13)
  postid   // VARCHAR(13)
  text     // TEXT
  datetime // DATETIME
}
$sql = 'CREATE TABLE `comments` (`uid` VARCHAR(13) NOT NULL, `postid` VARCHAR(13) NOT NULL, `text` TEXT NOT NULL, `datetime` DATETIME NOT NULL) ENGINE = MyISAM'; 

plus {
  uid      // VARCHAR(13)
  postid   // VARCHAR(13)
}
$sql = 'CREATE TABLE `plus` (`uid` VARCHAR(13) NOT NULL, `postid` VARCHAR(13) NOT NULL) ENGINE = MyISAM'; 

*/
?>