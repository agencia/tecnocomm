(function($) {
    // This script was written by Steve Fenton
    // http://www.stevefenton.co.uk/Content/Jquery-Two-Sided-Multi-Selector/
    // Feel free to use this jQuery Plugin

    var selectIds = new Array();
    var sortOnSelect = false;
    var nameModifier = "tsms";

    function AddDoubleClickEvents(targetName) {
        // Event handlers
	
		jQuery("#" + targetName).live("dblclick", function() {
            $(this).children(":selected").remove().appendTo("#" + targetName + nameModifier);
            SortOnSelect();
            AddDoubleClickEvents();
        });

		jQuery("#" + targetName + nameModifier).live("dblclick", function() {
            $(this).children(":selected").remove().appendTo("#" + targetName);
            SortOnSelect();
            AddDoubleClickEvents();
        });
    };

    function SortOnSelect(targetName) {
        if (sortOnSelect) {
            jQuery("#" + targetName + " option").tsort("", { attr: "value" });
            jQuery("#" + targetName + nameModifier + " option").tsort("", { attr: "value" });
        }
    };

    jQuery.fn.twosidedmultiselect = function(useTinySort) {

        return this.each(function() {

            var originalName = "";
            var arrayName = "";
            var modifiedName = "";

            if (useTinySort !== null) {
                sortOnSelect = useTinySort;
            }

            jQuery("form").submit(function() {
                for (var i = 0; i < selectIds.length; i++) {
                    jQuery("#" + selectIds[i] + " option").attr("selected", "selected");
                }
            });

            // Rename the old element and steal its name so the postback uses our element instead
            originalName = jQuery(this).attr("name");
            if (originalName.indexOf("[]") > -1) {
                arrayName = "[]";
                originalName = originalName.replace("[]", "");
            }
            modifiedName = originalName + nameModifier;
            var size = jQuery(this).attr("size");

            selectIds[selectIds.length] = originalName;

            jQuery(this).attr("id", modifiedName).attr("name", modifiedName);

            // Create our element to hold the selections and the buttons for moving elements
            var htmlBlock = "<div class=\"" + nameModifier + "options\">" +
				"<p class=\"AddOne\" rel=\"" + originalName + "\" title=\"Add Selected\">&rsaquo;</p>" +
				"<p class=\"AddAll\" rel=\"" + originalName + "\" title=\"Add All\">&raquo;</p>" +
				"<p class=\"RemoveOne\" rel=\"" + originalName + "\" title=\"Remove Selected\">&lsaquo;</p>" +
				"<p class=\"RemoveAll\" rel=\"" + originalName + "\" title=\"Remove All\">&laquo;</p>" +
				"</div>" +
				"<div class=\"" + nameModifier + "select\">" +
				"<select name=\"" + originalName + arrayName + "\" id=\"" + originalName + "\" size=\"" + size + "\"multiple=\"multiple\" size=\"8\" class=\"TakeOver\"></select>" +
				"</div>";

            jQuery(this).after(htmlBlock);
            jQuery(this).wrap("<div class=\"" + nameModifier + "select\" />");

            // Move existing selection to our elements

            jQuery("#" + modifiedName + " option:selected").remove().appendTo("#" + originalName);

            // Events

            AddDoubleClickEvents(originalName);

            jQuery("." + nameModifier + "options .AddOne").click(function() {
                var targetName = $(this).attr("rel");
                jQuery("#" + targetName + nameModifier + " option:selected").remove().appendTo("#" + targetName);
                SortOnSelect(targetName);
            });

            jQuery("." + nameModifier + "options .AddAll").click(function() {
                var targetName = $(this).attr("rel");
                jQuery("#" + targetName + nameModifier + " option").remove().appendTo("#" + targetName);
                SortOnSelect(targetName);
            });

            jQuery("." + nameModifier + "options .RemoveOne").click(function() {
                var targetName = $(this).attr("rel");
                jQuery("#" + targetName + " option:selected").remove().appendTo("#" + targetName + nameModifier);
                SortOnSelect(targetName);
            });

            jQuery("." + nameModifier + "options .RemoveAll").click(function() {
                var targetName = $(this).attr("rel");
                jQuery("#" + targetName + " option").remove().appendTo("#" + targetName + nameModifier);
                SortOnSelect(targetName);
            });
        });
    };
})(jQuery);