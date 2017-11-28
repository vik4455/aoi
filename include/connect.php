<?php
            $url = parse_url(getenv("CLEARDB_DATABASE_URL"));

            $server = $url["host"];
            $username = $url["user"];
            $password = $url["pass"];
            $db = substr($url["path"], 1);

            $conn = new mysqli($server, $username, $password, $db);
            
            if (mysqli_connect_errno()) {
                printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
            }

            /* return name of current default database */
            if ($result = $conn->query("SELECT DATABASE()")) {
            $row = $result->fetch_row();
            $result->close();
            }

/* change db to world db */
            $conn->select_db("heroku_88c65b266d41d8b");

            $conn->set_charset("utf8");
?>