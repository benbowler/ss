<div class="wrap">
    <div id="icon-edit" class="icon32"></div>
    <h2>Link Class &raquo; Manage</h2>
    
    <form id="post" name="post" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
        <div id="poststuff" class="metabox-holder">
            <div id="side-info-column" class="inner-sidebar"></div>
            <div id="post-body" class="has-sidebar">
                <div id="post-body-content" class="has-sidebar-content">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable" style="position: relative;">
                        <div class="postbox">
                            <h3 class="hndle">Name</h3>
                            <div class="inside">
                                <label class="hidden" for="name">Name</label>
                                <input type="text" name="name" value="<?php echo $this->data->name; ?>"  tabindex="1" style="width: 95%;" />
                                <p>The name / label to describe the class.</p>
                            </div>
                        </div>
                        
                        <div class="postbox">
                            <h3 class="hndle">Class</h3>
                            <div class="inside">
                                <label class="hidden" for="css">Class</label>
                                <input type="text" name="css" value="<?php echo $this->data->css; ?>" tabindex="2" style="width: 95%;" />
                                <p>The class name.</p>
                            </div>
                        </div>
                        
                        <div class="submit">
                            <input type="hidden" name="classID" value="<?php echo $this->data->classID; ?>" /> 
                            <input type="submit" name="submit" value="Save" /> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
 </div>