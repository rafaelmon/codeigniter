<?php header("Content-type:'text/css'");
?>
div.header {
    height: 100px;
    margin: 0 auto;
    overflow: hidden;
    position: relative;
    width: 1198px;
    z-index: 0;
}
div.header-jpeg {
    background-image: <?php echo $imagen_top; ?>;
    background-position: center center;
    background-repeat: no-repeat;
    height: 100px;
    left: 0;
    position: absolute;
    top: 0;
    width: 1198px;
    z-index: -1;
}

.logo {
    display: block;
    left: 30px;
    position: absolute;
    top: 9px;
    width: 100%;
}

h1.logo-name, h1.logo-name a, h1.art-logo-name a:link, h1.art-logo-name a:visited, h1.art-logo-name a:hover {
    color: #FFFFFF !important;
    font-family: "Palatino Linotype",Georgia,"Times New Roman",Times,Serif;
    font-size: 30px;
    margin: 0;
    padding: 0;
    text-decoration: none;
    text-transform: uppercase;
}
h1.logo-name {
    display: block;
    text-align: left;
}

.logo-text, .logo-text a {
    color: #FFFFFF !important;
    font-family: Palatino Linotype,Georgia,Times New Roman,Times,Serif;
    font-size: 17px;
    margin: 0;
    padding: 0;
    text-transform: none;
    font-weight: bold;
    text-decoration: none;
}
.logo-text {
    display: block;
    text-align: left;
}
.footer, .footer p, .footer a, .footer a:link, .footer a:visited, .footer a:hover {
    color: #D1E2F0;
    font-size: 12px;
}
.footer {
    overflow: hidden;
    position: relative;
    width: 100%;
}

.footer-t {
    background-color: #00438F;
    bottom: 50px;
    left: 0;
    position: absolute;
    right: 0;
    top: 0;
}
.footer-b {
    background-image: url("../images/footer_b.png");
    bottom: 0;
    height: 50px;
    left: 0;
    position: absolute;
    right: 0;
}
.footer-body {
    padding: 15px;
    position: relative;
}

.footer-text {
    margin: 0 10px;
}
.footer-text, .footer-text p {
    margin: 0;
    padding: 0;
    text-align: center;
}
.cleared {
    border: medium none;
    clear: both;
    float: none;
    font-size: 1px;
    margin: 0;
    padding: 0;
}
