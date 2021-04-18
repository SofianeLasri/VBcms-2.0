<?php
// Page d'initialisation
adminNavbarAddCategory("vbcms-blog-system", "blog");
adminNavbarAddItem("vbcms-blog-system", "fas fa-list", 'blog_list', "/vbcms-admin/blog/posts-list");
adminNavbarAddItem("vbcms-blog-system", "fas fa-pen", 'blog_create', "/vbcms-admin/blog/post-new");
adminNavbarAddItem("vbcms-blog-system", "fas fa-folder-open", 'blog_categories', "/vbcms-admin/blog/categories");
?>