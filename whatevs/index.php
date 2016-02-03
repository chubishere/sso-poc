<?php 

$domain = null;
handle_domain();

$token = null;
handle_token();
handle_logout();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head lang="<?php echo $str_language; ?>" xml:lang="<?php echo $str_language; ?>">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>Whatevs</title>
</head>

<body>
  <?php if( isset( $_COOKIE[ 'token' ] ) && is_token_in_db( $_COOKIE[ 'token' ] ) ): ?>
    <?php do_auth_done() ?>

  <?php elseif( isset( $_GET['token'] ) && is_token_in_db( $_GET['token'] ) ): ?>
    <?php do_auth_init() ?>

  <?php elseif( !isset( $_GET['is_authed'] ) || $_GET['is_authed'] == 0 ): ?>
    <?php do_login() ?>

  <?php endif ?>

  <hr/>

  <?php show_test_links() ?>

</body>
</html>

<?php function handle_domain(){
  global $domain;
  $match = null;
  preg_match( '/[a-z]+\.[a-z]+$/', $_SERVER['SERVER_NAME'], $match );
  if( is_array( $match ) && count( $match ) > 0 ) {
    $domain = $match[0];
  }
} ?>

<?php function handle_token(){
  global $domain;
  global $token;
  //when SSO sends us a token, we'll keep it in the cookie
  if( isset( $_GET['token'] ) ) {
    $token = $_GET['token'];
    setcookie( 'token', $_GET['token'], 0, '', '.'.$domain  );

    // comment this out to see the inbetween debug step of do_auth_init() below 
    header( "Location: /" );
  }
} ?>

<?php function handle_logout(){
  global $domain;
  //handle log out request
  if( isset( $_GET['clear_auth'] ) ) {
    //clear the cookie
    setcookie ("token", "", time() - 3600, '', '.'.$domain);
    header( "Location: /" );
  }
} ?>

<?php function do_login(){ ?>
  <?php global $token ?>
  <form action="http://sso:8888" method="POST">

    <p>Are you allowed?</p>

    <p>
      <input type="submit" name="login_allow" value="Yes"/>
      <input type="submit" name="login_reject" value="No"/>
    </p>

    <?php if( isset( $_GET['is_authed'] ) ): ?>
    <hr/>

    <p>
        <?php echo $_GET['is_in']? 'Already logged in': 'Just attempted log in at SSO'; ?>
        <?php echo $_GET['is_authed']? 'successfully': 'but failed'; ?>
    </p>

    <hr/>

    <?php $is_authed = ( isset( $_GET['is_authed'] ) && $_GET['is_authed'] == 1  )? 1 : 0 ?>
    <p>
    is authed:
    <input type="input" name="is_authed" value="<?php echo $is_authed ?>"/>
    </p>

    <p>
    token:
    <input type="input" name="token" value="<?php echo $token ? $token : 'no token' ?>"/>
    </p>
    <?php endif ?>

  </form>
<?php } ?>

<?php function do_auth_init(){ ?>

    <p>
      <?php echo $_GET['is_in']? 'Already logged in': 'Just attempted log in at SSO'; ?>
      <?php echo $_GET['is_authed']? 'successfully': 'but failed'; ?>
    </p>

    <hr/>

    <?php $is_authed = ( isset( $_GET['is_authed'] ) && $_GET['is_authed'] == 1  )? 1 : 0 ?>
    <p>
      is authed:
      <input type="input" name="is_authed" value="<?php echo $is_authed ?>"/>
    </p>

    <?php $token = ( isset( $_GET['token'] ) )? $_GET['token'] : 'no token'; ?>
    <p>
      token:
      <input type="input" name="token" value="<?php echo $token ?>"/>
    </p>

    <p>
      <?php setcookie( "token", $token ) ?>
      cookie set for key "token" 
    </p>
    <p>
    This page here for dev, it will not be seen in production for the user.
    </p>
    <a href="">Carry on</a>

  </form>
<?php } ?>

<?php function do_auth_done(){ ?>
  <p>I can see your cookie right now: </p>
  <p>
    <script type="text/javascript">
      document.write( 'Cookie: ' + document.cookie );
    </script>
  </p>
  <hr/>
  <?php //show_test_links() ?>
  <p>
    <form action="/">
      <input type="submit" name="clear_auth" value="Clear Auth" />
    </form>
  </p>
<?php } ?>

<?php 

function is_token_in_db( $token ){
  $stored_token = 'abc'; //this should come from DB or SSO server
  return $token == $stored_token;
}
?>

<?php function show_test_links(){ ?>
  <?php global $domain ?>
  <?php global $token ?>
  <a href="http://whatevs.local:8888">http://whatevs.local:8888</a><br/>
  <a href="http://one.whatevs.local:8888">http://one.whatevs.local:8888</a><br/>
  <a href="http://two.whatevs.local:8888">http://two.whatevs.local:8888</a><br/>
  <script>
  function whoevs_url() {
    var whoevs_domain = 'http://whoevs.local:8888'
    if( document.cookie.match( /^token/ ) ){
      return whoevs_domain + '?' + document.cookie;
    }
    return whoevs_domain;
  }
  </script>
  <a href="" onclick="window.location.href = whoevs_url(); return false;"><script>document.write(whoevs_url())</script></a><br/>
  <br/>
<?php } ?>
