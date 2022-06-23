<?php
/*
 *
 *   Example of API integration
 *   --------------------------
 *
 *   Pure PHP example... does not require any local RS elements (connects to RS via HTTP).
 *   This code would be on a client (non ResourceSpace) system.
 *
 *   For documentation please see: http://www.resourcespace.com/knowledge-base/api/
 *
*/

$private_key= "8c5bba1cd91c5efa314eb10ea41b13c5f5294484f6c93f7a7317539f5965f098"; # <---  From RS user edit page for the user to log in as
$user="jorge.perez"; # <-- RS username of the user you want to log in as

# Some example function calls.
#
#$query="user=" . $user . "&function=do_search&param1="; # <--- The function to execute, and parameters
#$query="user=" . $user . "&function=get_resource_field_data&param1=AM3626010000264"; # <--- The function to execute, and parameters
#$query="user=" . $user . "&function=create_resource&param1=1"; # <--- The function to execute, and parameters
#$query="user=" . $user . "&function=update_field&param1=1&param2=8&param3=Example"; # <--- The function to execute, and parameters
#$query="user=" . $user . "&function=delete_resource&param1=1"; # <--- The function to execute, and parameters
#$query="user=" . $user . "&function=copy_resource&param1=2"; # <--- The function to execute, and parameters
#$query="user=" . $user . "&function=get_resource_data&param1=2"; # <--- The function to execute, and parameters
#$query="user=" . $user . "&function=get_alternative_files&param1=2"; # <--- The function to execute, and parameters
#$query="user=" . $user . "&function=get_resource_types"; # <--- The function to execute, and parameters
#$query="user=" . $user . "&function=add_alternative_file&param1=2&param2=Test"; # <--- The function to execute, and parameters
#$query="user=" . $user . "&function=get_resource_path&param1=1&param2=2&param3=&param4=&param5=&param6=&param7=&param8=";
#$query="user=" . $user . "&function=get_resource_log&param1=2"; # <--- The function to execute, and parameters
#$query="user=" . $user . "&function=upload_file_by_url&param1=2&param2=&param3=&param4=&param5=" . urlencode("http://www.montala.com/img/slideshow/montala-bg.jpg"); # <--- The function to execute, and parameters
# Create resource, add a file and add metadata in one pass.
#$query="user=" . $user . "&function=create_resource&param1=1&param2=&param3=" . urlencode("http://www.montala.com/img/slideshow/montala-bg.jpg") . "&param4=&param5=&param6=&param7=" . urlencode(json_encode(array(1=>"Foo",8=>"Bar"))); # <--- The function to execute, and parameters
$query="user=" . $user . "&function=search_get_previews&param1=AM3626010000001&param2=&param3=&param4=0&param5=&param6=&param7=&param8=thm&param9=";


# Sign the query using the private key
$sign=hash("sha256",$private_key . $query);

# Make the request.
$results=file_get_contents("http://10.0.60.184/api/?" . $query . "&sign=" . $sign);
#echo htmlspecialchars($results);

$urls = array_column($results, 'url_thm');
print_r($urls);
