/**
 * Sends the browser detected timezone back to the server and displays the result
 * of the comparison to the user to make a decision (if necessary). 
 *
 * @package    local
 * @subpackage timezonecheck
 * @copyright  &copy; 2014 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     evan.irving-pease
 * @version    1.0
 */

M.local_timezonecheck = {

	detect: function(Y) {

    	YUI().use("io-base", "node", "anim", function(Y) {

    		// determines the time zone of the client browser
    		var browserzone = jstz.determine().name();

            Y.io(M.cfg.wwwroot+"/local/timezonecheck/detect.php?browserzone="+browserzone,{
                method : 'GET',
                on : {
                    success : function(id, o) {
                    	// was there a timezone change detected
            	        if (o.responseText) {
            	            // insert the response into the page header
            	            Y.one('#page-header').append(o.responseText);

            	            // add onclick event listeners to all the anchors
            	            Y.all('#timezone-warning a').each(function(a) {
            	            	a.on('click', function(e) {
            	            		e.preventDefault();
            						e.stopPropagation();

            						// ajax the choice back to the server
            						Y.io(a.get('href'));
            						
            						// get the timezone warning container
            						var container = Y.one('#timezone-container');
            						
            						// fade the container to nothing
            						var anim = new Y.Anim({
            						    node: container,
            						    to: { opacity: 0 }
            						});
            						
            						anim.on('end', function() {
            							// remove the hidden container
                						container.remove();
            						});
            						
            						anim.run();
            						
            	            		return false;
            	            	});
            	            });
            	        }
                    }
                },
                context : this
            });
    	});
    }
};