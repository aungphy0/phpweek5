<?php

  require_once('../../private/initialize.php');

  if(isset($_POST['submit'])) {

    if(!isset($_GET['id'])) {
      redirect_to('index.php');
    }
    $id = $_GET['id'];
    $agent_result = find_agent_by_id($id);
    $agent = db_fetch_assoc($agent_result);
    $sender['id'] = $current_user['id'];
    $encrypted_text = pkey_encrypt(h($_POST['plain_text']),$agent['public_key']);
    $sender_agent_result = find_agent_by_id($current_user['id']);
    $sender_agent = db_fetch_assoc($sender_agent_result);
    $signature = create_signature($encrypted_text, $sender_agent['private_key']);

    $message = [
      'sender_id' => $sender['id'],
      'recipient_id' => $agent['id'],
      'cipher_text' => $encrypted_text,
      'signature' => $signature

    ];


    $result = insert_message($message);
    if($result === true) {
      // Just show the HTML below.
    } else {
      $errors = $result;
    }

  } else {
    redirect_to('index.php');
  }


?>

<!doctype html>

<html lang="en">
  <head>
    <title>Message Dropbox</title>
    <meta charset="utf-8">
    <meta name="description" content="">
    <link rel="stylesheet" media="all" href="<?php echo DOC_ROOT . '/includes/styles.css'; ?>" />
  </head>
  <body>

    <a href="<?php echo url_for('/agents/index.php'); ?>">Back to List</a>
    <br/>

    <h1>Message Dropbox</h1>

    <div>
      <p><strong>The message was successfully encrypted and saved.</strong></p>

      <div class="result">
        Message:<br />
        <?php echo h($encrypted_text); ?><br />
        <br />
        Signature:<br />
        <?php echo h($signature); ?>
      </div>
    </div>

  </body>
</html>
