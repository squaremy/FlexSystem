<?php
  echo "hash: " . password_hash("Sp33dracer!", PASSWORD_BCRYPT);
  echo "verify: " . password_verify("Sp33dracer!", '$2y$10$ELelPeMdP0upEvz51P8C3uu7eK597v8p8.N0PSWelOttpGoe8PRUm');
?>
