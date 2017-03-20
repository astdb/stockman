<?php
    // buystock module accepts POST requests from homepage to
    //  - check price for a given stock
    //  - execute a purchase for that stock and update held stocks and account balance for this user

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

    // general libraries
    if( is_file('lib/lib.inc.php') ){
        include_once('lib/lib.inc.php');
    } else {
        print "m|Internal error #837429384679.";
        exit(1);
    }

    // if (isset user session) {
        $uid = 1;
        // request to check and return the price for a given stock
        if( isset( $_POST['buystockcode']) && isset( $_POST['checkprice']) ){
            $code = trim($_POST['buystockcode']);
            $cp = trim($_POST['checkprice']);

            if( strcmp($code, $cp) == 0 ) {
                $yf = new YahooFin();
                $price = $yf->getCurrentQuote($code);

                if($price > 0){
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

        // request to purchase a certain stock
        if( isset( $_POST['buystockcode']) && isset( $_POST['buystockamount']) && isset( $_POST['buyprice']) && isset($_POST['buystock']) ){
            $code = trim($_POST['buystockcode']);
            $amount = intval(trim($_POST['buystockamount']));
            $price = trim($_POST['buyprice']);
            $bstck = trim($_POST['buystock']);

            if( strcmp($code,"") != 0 && is_numeric($amount) && is_numeric($price) && (strcmp($code,$bstck) == 0) ){
                $total = ($price * 100) * $amount;

                // print "m|".$total; exit();

                // update balance
                $user_bal = -1;
                try {
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
                        print "m|<b>Sorry, we ran into an error (#32135468452)</b>";
                        exit(1);
                    }
                    $dbh = null;

                    // check funds
                    if( $user_bal >= 0 ){
                        if($total > $user_bal){
                            print "m|Insufficient funds to complete this transaction.";
                            exit(1);
                        }
                    } else {
                        print "m|<b>Sorry, we ran into an error (#65468454351)</b>";
                        exit(1);
                    }

                    // deduct purchase amount and update balance
                    // $bef_bal = $user_bal;
                    $user_bal = $user_bal - $total;

                    // print "m|Starting balance: ".print_amount($bef_bal).",Total = ".print_amount($total).", remaining bal: " . print_amount($user_bal); exit(0);

                    $dbh = $db_pdo->connect();
                    $stmt = $dbh->prepare("UPDATE tbl_user SET user_balance=:ubal WHERE user_id=:uid");
                    $stmt->bindParam(':ubal', $user_bal);
                    $stmt->bindParam(':uid', $uid);
                    
                    $bal_updated = false;
                    if( $stmt->execute() ) {
                        $bal_updated = true;
                    } else {
                        print "m|<b>Sorry, we ran into an error (#541654985)</b>";
                        exit(1);
                    }
                    $dbh = null;

                    // update this user's stock portfolio to reflect new purchase
                    $dbh = $db_pdo->connect();
                    $stmt = $dbh->prepare("SELECT sh_id, sh_stockcode, sh_amount FROM tbl_stock_holdings WHERE sh_stockcode=:skd AND user_id=:uid");
                    $stmt->bindParam(':skd', $code);
                    $stmt->bindParam(':uid', $uid);
                    
                    $stock_held = false;
                    $shid = -1;
                    $shamt = -1;
                    if( $stmt->execute() ) {
                        while( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
                            if( isset($row['sh_id']) && isset($row['sh_amount']) ) {
                                $shid = $row['sh_id'];
                                $shamt = $row['sh_amount'];
                                $stock_held = true;
                            }
                        }
                    } else {
                        print "m|<b>Sorry, we ran into an error (#32135468452)</b>";
                        exit(1);
                    }
                    $dbh = null;

                    if( $stock_held ){
                        // print "m|Stock held"; exit();
                        // update current stock
                        $amount = $amount + $shamt;

                        $dbh = $db_pdo->connect();
                        $stmt = $dbh->prepare("UPDATE tbl_stock_holdings SET sh_amount=:samt WHERE sh_id=:shid");
                        $stmt->bindParam(':shid', $shid);
                        $stmt->bindParam(':samt', $amount);
                        
                        $rec_updated = false;
                        if( $stmt->execute() ) {
                            $rec_updated = true;
                            print "r|record updated";
                            exit(0);
                        } else {
                            print "m|<b>Sorry, we ran into an error (#65484351321864)</b>";
                            exit(1);
                        }
                        $dbh = null;
                    } else {
                        // enter new stock record
                        $dbh = $db_pdo->connect();
                        $stmt = $dbh->prepare("INSERT INTO tbl_stock_holdings(sh_stockcode, sh_amount, user_id) VALUES(:shcode, :shamt, :shuid)");
                        $stmt->bindParam(':shcode', $code);
                        $stmt->bindParam(':shamt', $amount);
                        $stmt->bindParam(':shuid', $uid);
                        
                        $rec_added = false;
                        if( $stmt->execute() ) {
                            $rec_added = true;
                            print "r|record added";
                            exit(0);
                        } else {
                            print "m|<b>Sorry, we ran into an error (#89576584765)</b>";
                            exit(1);
                        }
                        $dbh = null;
                    }

                } catch (PDOException $e) {
                    print "m|<b>Sorry, we ran into an error (#6874653546542) " . $e->getMessage() . "<br/>";
                    $dbh = null;
                    exit(1);
                }
            }
        }

    // } else {
    //     header('Location: index.php');
    //     exit(1);
    // }