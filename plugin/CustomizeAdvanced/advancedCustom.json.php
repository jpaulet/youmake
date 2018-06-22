<?php

require_once '../../videos/configuration.php';
session_write_close();
header('Content-Type: application/json');
$obj = YouPHPTubePlugin::getObjectDataIfEnabled("CustomizeAdvanced");
//$obj = '{"doNotShowUploadMP4Button":true,"doNotShowImportMP4Button":false,"doNotShowImportLocalVideosButton":false,"doNotShowEncoderButton":false,"doNotShowEmbedButton":false,"doNotShowEncoderResolutionLow":false,"doNotShowEncoderResolutionSD":false,"doNotShowEncoderResolutionHD":false,"doNotShowLeftMenuAudioAndVideoButtons":false,"disableNativeSignUp":false,"disableNativeSignIn":false,"doNotShowWebsiteOnContactForm":false,"newUsersCanStream":false,"doNotIndentifyByEmail":false,"doNotIndentifyByName":false,"doNotIndentifyByUserName":false,"doNotUseXsendFile":false,"makeVideosInactiveAfterEncode":false,"usePermalinks":true,"showAdsenseBannerOnTop":false,"showAdsenseBannerOnLeft":true,"disableAnimatedGif":false,"unverifiedEmailsCanNOTLogin":false,"removeBrowserChannelLinkFromMenu":false,"uploadButtonDropdownIcon":"fas fa-video","uploadButtonDropdownText":"","EnableWavesurfer":true,"EnableMinifyJS":false,"disableShareAndPlaylist":false,"commentsMaxLength":"200","disableYoutubePlayerIntegration":false,"utf8Encode":false,"utf8Decode":false,"embedBackgroundColor":"white","userMustBeLoggedIn":false,"underMenuBarHTMLCode":{"type":"textarea","value":""},"encoderNetwork":""}';
echo json_encode($obj);
include $global['systemRootPath'].'objects/include_end.php';
