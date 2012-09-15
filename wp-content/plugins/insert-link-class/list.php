<div class="wrap">
    <?php
    if ($this->successMessage != '') {
        ?>
        <div class="updated fade"><p><?php echo $this->successMessage; ?></p></div>
        <?php
    }
    ?>
    
    <form method="post" action="admin.php?page=insert-link-class&cmd=save">
        <!-- Title and Buttons -->
        <h2>Link Classes</h2>
        <p>
            <a href="admin.php?page=insert-link-class&cmd=add" title="Add Class" class="button">Add Class</a>        
        </p>
        
        <!-- List -->
        <table class="widefat post fixed">
            <thead>
                <tr>
                    <th width="5%" class="manage-column column-title">&nbsp;</th>
                    <th class="manage-column column-title">Name / Label</th>
                    <th class="manage-column column-title">Class</th>                    
                </tr>
            </thead>
            <tbody>
            <?php
            if (count($this->data) > 0) {
                // Go through results
                foreach ($this->data as $result) {
                    ?>
                    <tr class="alternate iedit">
                        <td><input type="checkbox" name="classID[<?php echo $result->classID; ?>]" value="1" /></td>
                        <td>
                            <a href="admin.php?page=insert-link-class&cmd=edit&pKey=<?php echo $result->classID; ?>" title="Edit <?php echo $result->name; ?>">
                                <?php echo $result->name; ?>
                            </a>
                        </td>
                        <td><?php echo $result->css; ?></td>
                    </tr>
                    <?php
                } 
            } else {
                // No results
                ?>
                <tr class="alternate iedit">
                    <td colspan="3">
                        <center>
                            No custom classes defined. <a href="admin.php?page=insert-link-class&cmd=add" title="Add Class" class="button">Add Class</a>        
                        </center>
                    </td>
                </tr>
                <?php
            }           
            ?>
            </tbody>
        </table>
        
        <!-- Update -->
        <div class="submit">
            <input type="hidden" name="doAction" value="1" />
            <input type="submit" name="submit" value="Delete Checked" /> 
        </div>
    </form>
</div>