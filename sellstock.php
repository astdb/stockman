<?php
    // sellstock module accepts POST requests from homepage to
    //  - check price for a given stock
    //  - execute a sell for that stock and update held stocks and account balance for this user

    session_start();

    // database connection libraries
    if( is_file('conn/pdo_config.inc.php') ){
        include_once('conn/pdo_config.inc.php');
    } else {
        print "m|Internal error #98793485798.";
        exit(1);
    }

    // general libraries
    if( is_file('lib/YahooFin.inc.php') ){
        include_once('lib/YahooFin.inc.php');
    } else {
        print "m|Internal error #65468654.";
        exit(1);
    }

    if( is_file('lib/lib.inc.php') ){
        include_once('lib/lib.inc.php');
    } else {
        print "m|Internal error #837429384679.";
        exit(1);
    }

    // if (isset user session) {
        $uid = 1;
        // request to check and return the price for a given stock
        if( isset( $_POST['sellstockcode']) && isset( $_POST['checkprice']) ){
            $code = trim($_POST['sellstockcode']);
            $cp = trim($_POST['checkprice']);

            if( strcmp($code, $cp) == 0 ) {
                $yf = new YahooFin();
                $price = $yf->getCurrentQuote($code);

                if($price > 0) {
                    print "s|" . htmlspecialchars($code) . ": $" . $price . "|" . $price;
                    exit(0);
                } else {
                    print "m|Invalid code.";
                    exit(1);
                }
                
            } else {
                print "m|Invalid request.";
                exit(1);
            }
        }

        // request to sell a certain stock
        if( isset( $_POST['sellstockcode']) && isset( $_POST['sellstockamount']) && isset( $_POST['sellprice']) && isset($_POST['sellstock']) ){
            $code = trim($_POST['sellstockcode']);
            $amount = intval(trim($_POST['sellstockamount']));
            $price = trim($_POST['sellprice']);
            $bstck = trim($_POST['sellstock']);

            if( strcmp($code,"") != 0 && is_numeric($amount) && is_numeric($price) && (strcmp($code,$bstck) == 0) ){
                $total = ($price * 100) * $amount;

                // print "m|".$total; exit();

                // update balance
                $user_bal = -1;
                try {
                    // get currently held stock of this code for this user
                    $dbh = $db_pdo->connect();
                    $stmt = $dbh->prepare("SELECT sh_id, sh_stockcode, sh_amount FROM tbl_stock_holdings WHERE sh_stockcode=:skd AND user_id=:uid");
                    $stmt->bindParam(':skd', $code);
                    $stmt->bindParam(':uid', $uid);
                    
                    $stock_held = false;
                    if( $stmt->execute() ) {
                        while( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
                            if(isset($row['sh_amount'])) {
                                $stock_held = true;
                                $user_bal = $row['sh_amount'];
                            }
                        }
                    } else {
                        print "m|<b>Sorry, we ran into an error (#76547657634)</b>";
                        exit(1);
                    }
                    $dbh = null;

                    // check stock
                    if( $user_bal > 0 ){
                        if($amount > $user_bal){
                            print "m|Insufficient ".$code." stock held to complete this sell transaction (".$user_bal.").";
                            exit(1);
                        }
                    } else {
                        print "m|<b>Sorry, we ran into an error (#74765375437)</b>";
                        exit(1);
                    }

                    // add sell amount and update cash balance
                    // get balance
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
                        print "m|<b>Sorry, we ran into an error (#5437636745)</b>";
                        exit(1);
                    }
                    $dbh = null;
                    
                    // add the sold amount and update balance
                    $user_bal = $user_bal + $total;

                    $dbh = $db_pdo->connect();
                    $stmt = $dbh->prepare("UPDATE tbl_user SET user_balance=:ubal WHERE user_id=:uid");
                    $stmt->bindParam(':ubal', $user_bal);
                    $stmt->bindParam(':uid', $uid);
                    
                    $bal_updated = false;
                    if( $stmt->execute() ) {
                        $bal_updated = true;
                    } else {
                        print "m|<b>Sorry, we ran into an error (#7843548935)</b>";
                        exit(1);
                    }
                    $dbh = null;

                    // update this user's stock portfolio to reflect new sell
                    $dbh = $db_pdo->connect();
                    $stmt = $dbh->prepare("SELECT sh_id, sh_stockcode, sh_amount FROM tbl_stock_holdings WHERE sh_stockcode=:skd AND user_id=:uid");
                    $stmt->bindParam(':skd', $code);
                    $stmt->bindParam(':uid', $uid);
                    
                    $shid = -1;
                    $shamt = -1;
                    if( $stmt->execute() ) {
                        while( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
                            if( isset($row['sh_id']) && isset($row['sh_amount']) ) {
                                $shid = $row['sh_id'];
                                $shamt = $row['sh_amount'];
                            }
                        }
                    } else {
                        print "m|<b>Sorry, we ran into an error (#32135468452)</b>";
                        exit(1);
                    }
                    $dbh = null;
                    
                    // update current stock
                    $newamount = $shamt - $amount;
                    // print "m|Sell: ".$amount." Held: ".$shamt." New: ".$newamount;

                    if($newamount > 0) {
                        // update stock record
                        $dbh = $db_pdo->connect();
                        $stmt = $dbh->prepare("UPDATE tbl_stock_holdings SET sh_amount=:samt WHERE sh_id=:shid");
                        $stmt->bindParam(':shid', $shid);
                        $stmt->bindParam(':samt', $newamount);
                        // $stmt->bindParam(':shid', $uid);
                        
                        $rec_updated = false;
                        if( $stmt->execute() ) {
                            $rec_updated = true;
                            print "r|record updated";
                            exit(0);
                        } else {
                            print "m|<b>Sorry, we ran into an error (#5468763215)</b>";
                            exit(1);
                        }
                        $dbh = null;
                    } else {
                        // delete stock record
                        $dbh = $db_pdo->connect();
                        $stmt = $dbh->prepare("DELETE FROM tbl_stock_holdings WHERE sh_id=:shid");
                        $stmt->bindParam(':shid', $shid);

                        $rec_updated = false;
                        if( $stmt->execute() ) {
                            $rec_updated = true;
                            print "r|record updated";
                            exit(0);
                        } else {
                            print "m|<b>Sorry, we ran into an error (#56476365423)</b>";
                            exit(1);
                        }
                        $dbh = null;
                    }

                } catch (PDOException $e) {
                    print "m|<b>Sorry, we ran into an error (#76588764653654) " . $e->getMessage() . "<br/>";
                    $dbh = null;
                    exit(1);
                }
            }
        }

    // } else {
    //     header('Location: index.php');
    //     exit(1);
    // }