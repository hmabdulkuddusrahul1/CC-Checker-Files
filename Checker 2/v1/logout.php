<?php
session_start();
session_destroy();
header('location: ./?session_destroyed=true&__user_loggedOut=true');