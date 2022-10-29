<?php
$ip_address = $api->IP_address();
$remember_me = $api->Read('remember_me', 'set', 'ip_address', "$ip_address");
if (!empty($remember_me)) {
   $email = $remember_me[0]->email;
   $get_user = $api->Read('user', 'set', 'email', "$email");
   $_SESSION['users'] = $get_user;
}