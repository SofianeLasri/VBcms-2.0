<script type="text/javascript">
	$( document ).ready(function() {
		loadNavbar(0,0);
	});

	async function loadNavbar(parentId,state){
		await $.get("<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/backTasks/?loadClientNavbar=all", function(data) {
			var navbarItems = JSON.parse(data);
			jQuery.each(JSON.parse(data), function(index){
				if (navbarItems[index]["parentId"]!=0 && $("#navbar-item-"+navbarItems[index]["parentId"]).attr("type")=="link") {
					console.log("test");
					$("#navbar-item-"+navbarItems[index]["parentId"]).addClass("dropdown");
					$("#navbar-item-"+navbarItems[index]["parentId"]).attr("type", "dropdown");
					$("#navbar-item-"+navbarItems[index]["parentId"]+" > a").addClass("dropdown-toggle");
					$("#navbar-item-"+navbarItems[index]["parentId"]+" > a").attr("role", "button");
					$("#navbar-item-"+navbarItems[index]["parentId"]+" > a").attr("id", "navDropdownLink-"+navbarItems[index]["parentId"]);
					$("#navbar-item-"+navbarItems[index]["parentId"]+" > a").attr("data-toggle", "dropdown");
					$("#navbar-item-"+navbarItems[index]["parentId"]).append("<div id='navDropdown-"+navbarItems[index]["parentId"]+"' class='dropdown-menu'></div>");
					
					$("#navDropdown-"+navbarItems[index]["parentId"]).append('\
					<li id="navbar-item-'+navbarItems[index]["id"]+'" type="link" class="nav-item active">\
						<a class="text-dark" href="'+navbarItems[index]["value2"]+'" target="'+navbarItems[index]["value3"]+'">'+navbarItems[index]["value1"]+'</a>\
					</li>');
				} else if (navbarItems[index]["parentId"]!=0){
					$("#navDropdown-"+navbarItems[index]["parentId"]).append('\
					<li id="navbar-item-'+navbarItems[index]["id"]+'" type="link" class="nav-item active">\
						<a class="text-dark" href="'+navbarItems[index]["value2"]+'" target="'+navbarItems[index]["value3"]+'">'+navbarItems[index]["value1"]+'</a>\
					</li>');
				} else {
					$("#navbar-itemParent-"+navbarItems[index]["parentId"]).append('\
					<li id="navbar-item-'+navbarItems[index]["id"]+'" type="link" class="nav-item active">\
						<a class="nav-link" href="'+navbarItems[index]["value2"]+'" target="'+navbarItems[index]["value3"]+'">'+navbarItems[index]["value1"]+'</a>\
					</li>');
				}
				
			});
		});
	}
</script>