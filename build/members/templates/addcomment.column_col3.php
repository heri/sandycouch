<?php
    $words = $this->words;
    $ww = $this->ww;
    $layoutbits = new MOD_layoutbits();
    
    $commenter = $this->loggedInMember->Username;
    $member = $this->member;
    $Username = $member->Username;

    $syshcvol = PVars::getObj('syshcvol');
    $ttc = $syshcvol->LenghtComments;
    $max = count($ttc);
    $comments = $member->get_comments_commenter($this->loggedInMember->id);
    $TCom = (isset($comments[0])) ? $comments[0] : false;
    $edit_mode = $TCom;
    
    // values from previous form submit
    if (!$mem_redirect = $this->layoutkit->formkit->getMemFromRedirect()) {
        // this is a fresh form
        $ttLenght = ($TCom) ? explode(',',$TCom->Lenght) : array();
    } else {
        // last time something went wrong.
        // recover old form input.
        $vars = $mem_redirect->post;
        $TCom = new stdClass();
        $ttLenght = array();
        for ($ii = 0; $ii < $max; $ii++) {
            $chkName = "Comment_" . $ttc[$ii];
            if (isset($vars[$chkName])) {
                $ttLenght[] = $ttc[$ii];
            }
        }
        if (isset($vars['Quality'])) {
            $TCom->comQuality = $vars['Quality'];
        }
        if (isset($vars['TextWhere'])) {
            $TCom->TextWhere = $vars['TextWhere'];
        }
        if (isset($vars['TextFree'])) {
            $TCom->TextFree = $vars['TextFree'];
        }
    }

    // Remove injected tags from old comments, so users don't have to edit HTML
    // that they didn't write
    $replacePatterns = array(
        '/<hr>/',
        '/<hr \/>/',
        '/<br>/',
        '/<br \/>/',
        '/<font color=gray><font size=1>(comment date .*)<\/font><\/font>/'
    );
    $replacements = array(
        "\n\n",
        "\n\n",
        "\n",
        "\n",
        '$1'
    );
    if (isset($TCom->TextFree)) {
        $textFreeWashed = preg_replace($replacePatterns, $replacements,
            $TCom->TextFree);
    } else {
        $textFreeWashed = "";
    }
    if (isset($TCom->TextWhere)) {
        $textWhereWashed = preg_replace($replacePatterns, $replacements,
            $TCom->TextWhere);
    } else {
        $textWhereWashed = "";
    }

    $mem_redirect = $this->layoutkit->formkit->getMemFromRedirect();
    
    $page_url = PVars::getObj('env')->baseuri . implode('/', PRequest::get()->request);
    
    $formkit = $this->layoutkit->formkit;
    $callback_tag = $formkit->setPostCallback('MembersController', 'commentCallback');
    
    ?>
    
    
    <?php
    // Display errors from last submit	
    if (isset($vars['errors']) && !empty($vars['errors']))
    {
        foreach ($vars['errors'] as $error)
        {
            echo '<div class="error">'.$words->get($error).'</div>';
        }
    }
    
    // Display the form to propose to add a comment	
    ?>
    
    <form method="post" name="addcomment" OnSubmit="return DoVerifySubmit('addcomment');">
    <?=$callback_tag ?>
    <fieldset>
    <legend><?=(!$edit_mode) ? $words->get("AddComments") : $words->get("EditComments")?></legend>
    <input name="IdMember" value="<?=$member->id?>" type="hidden" />
        <table valign="center" >
          <tr>
            <td colspan=2><h3><?=$words->get("CommentQuality" , $Username)?></h3><br /><?=$words->get("RuleForNeverMetComment")?></td>
          </tr>
          <tr>
            <td>
                <select name="Quality">
                    <option value="Good"
                    <?=(isset($TCom->comQuality) && $TCom->comQuality == "Good") ? " selected " : ""?>
                    >
                    <?=$words->get("CommentQuality_Good")?></option>
                    <option value="Neutral"
                    <?=(!isset($TCom->comQuality) || $TCom->comQuality == "Neutral") ? " selected " : ""?>
                    >
                    <?=$words->get("CommentQuality_Neutral")?></option>
                    <option value="Bad"
                    <?=(isset($TCom->comQuality) && $TCom->comQuality == "Bad") ? " selected " : ""?>
                    >
                    <?=$words->get("CommentQuality_Bad")?></option>
                </selected>
            </td>
            <td>
                <p class="grey"><?=$words->get("CommentQualityDescription", $Username, $Username, $Username)?></p>
            </td>
          </tr>
        <tr>
        <td colspan=2>
            <h3><?=$words->get("CommentLength", $Username)?></h3>
        </td>
        </tr>
        <tr>
        <td>
        <table valign=center >
        <?php
        for ($ii = 0; $ii < $max; $ii++) {
            $chkName = "Comment_" . $ttc[$ii];
            echo '<tr><td><label for="' . $chkName . '">' . $words->get($chkName) . "</td>";
            echo '<td><input type="checkbox" id="' . $chkName . '" name="' . $chkName . '"';
            if (in_array($ttc[$ii], $ttLenght))
            echo " checked ";
            echo ">\n</td>\n";
        }
        ?>
        </table>
        </td>
        <td>
        <p class="grey"><?php echo $words->get("CommentLengthDescription", $Username, $Username, $Username) ?></p>
        </td>
    </tr>
    <tr>
        <td colspan="2"><h3><label for="TextWhere"><?php echo $words->get("CommentsWhere", $Username) ?></label></h3></td>
    </tr>
    <tr>
        <td><textarea name="TextWhere" id="TextWhere" cols="40" rows="3"><?php echo $textWhereWashed; ?></textarea></td>
        <td><p class="grey"><?php echo $words->get("CommentsWhereDescription", $Username) ?></p></td>
    </tr>
    <tr>
        <td colspan="2"><h3><label for="Commenter"><?php echo $words->get("CommentsCommenter") ?></label></h3></td>
    </tr>
    <tr>
        <td><textarea name="TextFree" id="TextFree" cols="40" rows="8"><?php echo $textFreeWashed; ?></textarea></td>
        <td style="vertical-align=top"><p class="grey"><?php echo $words->get("CommentsCommenterDescription", $Username) ?></p></td>
    </tr>
    <tr><td colspan="2">
        <input type="hidden" value="<?php echo $member->id?>" name="cid">
        <input type="hidden" name="action" value="add">
        <input type="submit" id="submit" name="valide" value="submit"></td>
    </tr>
    </table>
    </fieldset>
    </form>

    <script type="text/javascript">
        function DoVerifySubmit(nameform) {
        nevermet=document.forms[nameform].elements['Comment_NeverMetInRealLife'].checked;
            if ((document.forms[nameform].elements['Quality'].value!='Negative') && (nevermet)) {
               alert('<?=addslashes($words->getSilent("RuleForNeverMetComment"))?>');
               return (false);
            }
            return(true);
        }
    </script>
    <?=$words->flushBuffer();?>
    </div>
