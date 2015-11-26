<!doctype html>
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<!--  PHP Portfolio Art Gallery Exhibit Showcase (PPAGES)  -->
<!--  http://centerkey.com/ppages                          -->
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<html>
<head>
<meta charset=utf-8>
<meta name=apple-mobile-web-app-title content="PPAGES">
<title>PPAGES &bull; PHP Portfolio Art Gallery Exhibit Showcase</title>
<link rel=icon             href="graphics/bookmark.png">
<link rel=apple-touch-icon href="graphics/mobile-home-screen.png">
<link rel=stylesheet       href="https://cdn.jsdelivr.net/fontawesome/4/css/font-awesome.min.css">
<link rel=stylesheet       href="https://cdn.jsdelivr.net/dna.js/0/dna.css">
<link rel=stylesheet       href="style.css">
<script src="https://apis.google.com/js/plusone.js"></script>
</head>
<body>

<div class=header>
   <div>
      <button data-href="https://github.com/center-key/ppages/tree/master/releases">
         Downloads
      </button>
   </div>
   <h1>PHP Portfolio Art Gallery Exhibit Showcase (PPAGES)</h1>
   <h2>A web application to manage and display a photo gallery.</h2>
</div>

<div class=col1>

<div class=block1>
   <h3>Build an Art Exhibit</h3>
   <a href="http://centerkey.com/ppages/"><img class=ppages-logo
      src="graphics/ppages-logo.png" alt="PPAGES Logo"></a>
   PPAGES is an open source software package for displaying a portfolio of photos
   on a website in a clean, professional style.&nbsp;  Use PPAGES to create an
   online exhibit for an artist or organization.<br>
   <br>
   Example Websites:
   <div class=indent>
      <a href="http://www.christopherpilafian.com/"
         onclick="this.target='_blank';">christopherpilafian.com</a>,
      <a href="http://www.frazierart.com/"
         onclick="this.target='_blank';">frazierart.com</a>,
      <a href="http://www.mgsteiner.com/"
         onclick="this.target='_blank';">mgsteiner.com</a>
   </div>
</div>

<div class=block2>
   <h3>Just the Facts</h3>
   <div class="box box-right"><a href="gallery"
      onclick="this.target='_blank';"><img alt="Screnshot Thumbnail"
      src="graphics/ppages-screenshots.png"><br>Screenshots</a>
   </div>
   PPAGES System Requirements:<ul>
      <li>Website running PHP 5
      <li>FTP access to upload files for the initial installation
   </ul>
   PPAGES Ingredients:<ul>
      <li>Gallery automatically fills width of user's browser
      <li>Slideshow viewer allows users to easily advance to next image
      <li>Contact form for sending a message to the artist
      <li>Gallery Management Console is password protected
      <li>Paswords are sent encrypted (SHA-1) and encrypted a second time before
         storing on the server
      <li>Upload multiple images files at one time
      <li>Automatic thumbnail generation
      <li>Over 50 fonts to choose from for the title
      <li>NoSQL &mdash; no relational database to setup or maintain
      <li>Data is stored in simple JSON text files
      <li>Open Source (<a href="http://www.gnu.org/licenses/gpl.html"
         onclick="this.target='_blank';">GPL</a>) &mdash; you have the
         freedom to examine, copy, and modify the code
      <li>Free &mdash; as in free beer
   </ul>
   PPAGES is not for social photo sharing.&nbsp;  It is intended to showcase an
   artist's work, and as such does not include social features such as user
   comments or privacy settings.<br>
   <br>
   The PPAGES project is built on the work of others, including:<ul>
      <li><a href="http://www.digitalia.be/software/slimbox2">Slimbox 2</a>
      <li><a href="http://jquery.com/">jQuery</a>
      <li><a href="http://valums.com/ajax-upload/">Valums File Uploader (Ajax Upload)</a>
      <li><a href="https://www.google.com/fonts">Google Fonts</a>
      <li><a href="http://www.movable-type.co.uk/scripts/sha1.html">SHA-1 Cryptographic Hash Algorithm</a>
   </ul>
</div>

</div>  <!-- end col1 -->
<div class=col2>

<div class=block3>
   <h3>Get Going</h3>
   Install and Setup:<ol>
      <li><a href="https://github.com/center-key/ppages/raw/master/releases/ppages-v0.0.3.zip"
         class=external-site>Download</a>
         and unzip the PPAGES install file.
      <li>Move the <code>gallery</code> folder into the local copy of your website
         files and then FTP the <code>gallery</code> folder up to your website.
      <li>Open the <i>Gallery Management Console</i> by appending
         <code>gallery/console</code> to your home page URL.*
      </ol>
   * Example: <small>http://www.example.com/gallery/console</small><br>
   <br>
   When you first go to the console, you will be
   prompted to create a user account for yourself.&nbsp;  Once in the console,
   upload photos or use the "Create New User Account" feature to create an account
   for the artist to upload photos directly.<br>
</div>

<div class=block4>
   <h3>News</h3>
   <p>
      <div class=date>April 17, 2014</div>
      Code is transfered over to GitHub.
   </p>
   <p>
      <div class=date>March 12, 2012</div>
      Beta version 0.0.3 with layout and styling improvements is released.
   </p>
   <p>
      <div class=date>January 2, 2011</div>
      Beta version 0.0.2 of PPAGES is released.
   </p>
</div>

<div class=block5>
   <h3>Community</h3>
   Forums <i>(not yet ready)</i>:
   <div class=indent>
      <a href="https://sites.google.com/site/ppagesproject/"
         title="sites.google.com/site/ppagesproject">Google Sites (ppagesproject)</a>
   </div>
   <br>
   Mailing List:
   <div class=indent>
      <a href="http://groups.google.com/group/ppages"
         title="groups.google.com/group/ppages">Google Project (ppages)</a>
   </div>
   <br>
   Code Hosting:
   <div class=indent>
      <a href="https://github.com/center-key/ppages">GitHub (ppages)</a>
   </div>
</div>

</div>  <!-- end col2 -->

<div class=footer>
   <div><div id=social-buttons></div></div>
   centerkey.com/ppages
</div>
<a href="https://github.com/center-key/ppages">
   <img id=github-banner src="https://s3.amazonaws.com/github/ribbons/forkme_right_gray_6d6d6d.png"
      alt="Fork me on GitHub">
</a>

<script src="https://cdn.jsdelivr.net/jquery/2/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/dna.js/0/dna.min.js"></script>
</body>
</html>
