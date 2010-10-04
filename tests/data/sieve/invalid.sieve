require ["fileinto", "copy"];

if allof(
        not header :contains "List-ID" "",
        not header :contains "from" ["internal@example.com"]
    )
    redirect :copy "contact@example.com";
}
else {
    keep;
}
