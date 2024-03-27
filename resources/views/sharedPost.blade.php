<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta property="og:title" content="<?php echo htmlspecialchars($article->title); ?>" />
  <meta property="og:description" content="<?php echo htmlspecialchars(mb_strimwidth($article->content, 0, 200, "...")); ?>" />
  <meta property="og:image" content="<?php echo htmlspecialchars($article->banner); ?>" />
  <meta property="og:url" content="https://www.channeltwenty.com/" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Redirect Page</title>
</head>

<body>

  <p>Redirecting...</p>

  <script>
    // JavaScript redirection
    setTimeout(function() {
    //   window.location.href = "https://channeltwenty.com/news/<?php echo urlencode($article->slug); ?>"; // Replace "https://channeltwenty.com/news/" with the base URL and $article->slug with the dynamic slug
    }, 1000); // Redirect after 3 seconds, you can adjust this value as needed
  </script>
</body>

</html>
