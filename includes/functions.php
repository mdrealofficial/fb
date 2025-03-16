<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

// Facebook App configuration
define('FB_APP_ID', 'YOUR_APP_ID'); // Replace with your App ID
define('FB_APP_SECRET', 'YOUR_APP_SECRET'); // Replace with your App Secret
define('FB_REDIRECT_URL', 'http://localhost/facebook-poster/login.php'); // Change to your domain
define('FB_PAGE_ID', 'YOUR_PAGE_ID'); // Replace with your Facebook Page ID

// Initialize Facebook SDK
function getFacebookInstance() {
    $fb = new Facebook([
        'app_id' => FB_APP_ID,
        'app_secret' => FB_APP_SECRET,
        'default_graph_version' => 'v17.0',
        'persistent_data_handler' => 'session'
    ]);
    
    return $fb;
}

// Get Facebook login URL
function getFacebookLoginUrl() {
    $fb = getFacebookInstance();
    $helper = $fb->getRedirectLoginHelper();
    
    $permissions = ['pages_manage_posts', 'pages_read_engagement', 'pages_manage_metadata'];
    $loginUrl = $helper->getLoginUrl(FB_REDIRECT_URL, $permissions);
    
    return $loginUrl;
}

// Get access token from callback
function getAccessToken() {
    $fb = getFacebookInstance();
    $helper = $fb->getRedirectLoginHelper();
    
    try {
        $accessToken = $helper->getAccessToken();
        return $accessToken;
    } catch(FacebookResponseException $e) {
        return "Graph returned an error: " . $e->getMessage();
    } catch(FacebookSDKException $e) {
        return "Facebook SDK returned an error: " . $e->getMessage();
    }
}

// Get long-lived access token
function getLongLivedAccessToken($accessToken) {
    $fb = getFacebookInstance();
    
    try {
        $oAuth2Client = $fb->getOAuth2Client();
        $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
        return $longLivedAccessToken;
    } catch(FacebookSDKException $e) {
        return "Error getting long-lived access token: " . $e->getMessage();
    }
}

// Get page access token
function getPageAccessToken($accessToken) {
    $fb = getFacebookInstance();
    
    try {
        $response = $fb->get('/me/accounts', $accessToken);
        $pages = $response->getGraphEdge()->asArray();
        
        foreach ($pages as $page) {
            if ($page['id'] == FB_PAGE_ID) {
                return $page['access_token'];
            }
        }
        return null;
    } catch(FacebookResponseException $e) {
        return "Graph returned an error: " . $e->getMessage();
    } catch(FacebookSDKException $e) {
        return "Facebook SDK returned an error: " . $e->getMessage();
    }
}

// Post image and text to Facebook page
function postToFacebook($imageUrl, $text, $comment, $pageAccessToken) {
    global $conn;
    $fb = getFacebookInstance();
    
    try {
        // Post image with caption
        $response = $fb->post(
            '/' . FB_PAGE_ID . '/photos',
            [
                'url' => $imageUrl,
                'caption' => $text,
            ],
            $pageAccessToken
        );
        
        $graphNode = $response->getGraphNode();
        $postId = $graphNode['id'];
        
        // Add first comment if provided
        if (!empty($comment)) {
            $commentResponse = $fb->post(
                '/' . $postId . '/comments',
                ['message' => $comment],
                $pageAccessToken
            );
        }
        
        // Save to database
        $stmt = $conn->prepare("INSERT INTO posts (post_id, image_url, post_text, comment_text) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $postId, $imageUrl, $text, $comment);
        $stmt->execute();
        
        return [
            'success' => true,
            'post_id' => $postId,
            'message' => 'Posted successfully!'
        ];
    } catch(FacebookResponseException $e) {
        return [
            'success' => false,
            'message' => 'Graph returned an error: ' . $e->getMessage()
        ];
    } catch(FacebookSDKException $e) {
        return [
            'success' => false,
            'message' => 'Facebook SDK returned an error: ' . $e->getMessage()
        ];
    }
}

// Get recent posts from database
function getRecentPosts() {
    global $conn;
    
    $sql = "SELECT * FROM posts ORDER BY posted_at DESC LIMIT 10";
    $result = $conn->query($sql);
    
    $posts = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
    }
    
    return $posts;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['fb_access_token']) && !empty($_SESSION['fb_access_token']);
}
?>
