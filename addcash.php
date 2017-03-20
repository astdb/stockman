<?php
    // addcash module accepts a POST request from homepage to add a specified cash amount to the user's balance

    session_start();

    // database connection libraries
    if( is_file('conn/pdo_config.inc.php') ){
        include_once('conn/pdo_config.inc.php');
    }

    // general libraries
    if( is_file('lib/lib.inc.php') ){
        include_once('lib/lib.inc.php');
    } 

    // TODO: check login/get UserID
    // if( isset(user_session) && isset($_POST['addcashamount']) ){
        $uid = 1;
        $cash = intval($_POST['addcashamount']);

        // set maximum balance to trillion dollars
        $MAX_BAL = 999999999; 

        if( !is_numeric($cash) ){
            print "m|Invalid cash amount.";
            exit(1);
        }

        // get current balance
        $user_bal = -1;
        try {   
            $dbh = $db_pdo->connect();
            $stmt = $dbh->prepare("SELECT user_balance FROM tbl_user WHERE user_id=:uid");
            $stmt->bindParam(':uid', $uid);
            
            if( $stmt->execute() ) {
                while( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
                    if(isset($row['user_balance'])) {
                        $user_bal = $row['user_balance'];
                    }
                }
            } else {
                print "m|<b>Sorry, we ran into an error (#32135468452)</b>";
                exit(1);
            }
            $dbh = null;
        } catch (PDOException $e) {
            print "m|<b>Sorry, we ran into an error (#2132168654) " . $e->getMessage() . "<br/>";
            $dbh = null;
            exit(1);
        }

        if ( $user_bal >= 0 && $user_bal < $MAX_BAL ) {
            $user_bal = $user_bal + ($cash*100);
            
            if ( $user_bal < $MAX_BAL ){
                // update balance
                try {   
                    $dbh = $db_pdo->connect();
                    $stmt = $dbh->prepare("UPDATE tbl_user SET user_balance=:ubal");
                    $stmt->bindParam(':ubal', $user_bal);
                    
                    if( $stmt->execute() ) {
                        print "r|redirect";
                        exit(0);
                    } else {
                        print "m|<b>Sorry, we ran into an error (#541654985)</b>";
                        exit(1);
                    }
                    $dbh = null;
                } catch (PDOException $e) {
                    print "m|<b>Sorry, we ran into an error (#12346843541354) " . $e->getMessage() . "<br/>";
                    $dbh = null;
                    exit(1);
                }
            }
        }
    // }