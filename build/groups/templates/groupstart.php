<?php foreach ($this->getMessages() as $message) : ?>
<p><?= $words->get($message); ?>
<?php endforeach; ?>
<?php
$group_name_html = htmlspecialchars($this->getGroupTitle(), ENT_QUOTES); 
$purifier = MOD_htmlpure::getBasicHtmlPurifier();
?>

<div id="groups">
    <div class="subcolumns">
        <div class="c62l">
            <div class="subcl">
                <div class="row floatbox">
                    <?= ((strlen($this->group->Picture) > 0) ? "<img class=\"float_left framed\" src='groups/realimg/{$this->group->getPKValue()}' width=\"100px\" alt='Image for the group {$group_name_html}' />" : ''); ?>
                    <h3><?= $words->get('GroupDescription'); ?></h3>
                    <p><?= $purifier->purify(nl2br($this->group->getDescription())) ?></p>
                </div> <!--row floatbox -->

                <h3><?= $words->getFormatted('ForumRecentPostsLong');?></h3>
                <div class="row floatbox">
                    <?= $Forums->showExternalGroupThreads($group_id); ?>
                </div> <!-- floatbox -->
                <?php
                $shouts = new ShoutsController();
    	        $shouts->shoutsList('groups',$group_id);
                ?>
                
            </div> <!-- subcl -->
        </div> <!-- c62l -->
        
        <div class="c38r">
            <div class="subcr">
            
                <?php
                    if (!APP_user::isBWLoggedIn('NeedMore,Pending')) : ?>
                <h3><?= $words->get('GroupsJoinNamedGroup', $group_name_html); ?></h3>
                    <?= $words->get('GroupsJoinLoginFirst'); ?>
                <?php else : ?>
                <h3><?= ((!$this->isGroupMember()) ? $words->get('GroupsJoinNamedGroup', $group_name_html) : $words->get('GroupsLeaveNamedGroup', $group_name_html) ) ?></h3>
                <div class="row clearfix">
                    <a class="bigbutton" href="groups/<?=$this->group->id ?>/<?= (($this->isGroupMember()) ? 'leave' : 'join' ); ?>"><span><?= ((!$this->isGroupMember()) ? $words->get('GroupsJoinTheGroup') : $words->get('GroupsLeaveTheGroup') ); ?></span></a>
                </div>
                <?php endif; ?>
                <h3><?= $words->get('GroupOwner'); ?></h3>
                <div class="floatbox">
                    <div class="center float_left">
                        <?php echo (($member =$this->group->getGroupOwner()) ? (MOD_layoutbits::PIC_50_50($member->Username) ."<br /><a href=\"members/".$member->Username ."\">" .$member->Username ."</a>")  : $words->get('GroupsNoOwner')); ?>
                    </div>
                </div>
                <h3><?= $words->get('GroupMembers'); ?></h3>
                <div class="floatbox">
                    <?php $memberlist_widget->render() ?>
                </div>
                <strong><a href="groups/<?= $group_id.'/members'; ?>"><?= $words->get('GroupSeeAllMembers'); ?></a></strong>
                
            </div> <!-- subcr -->
        </div> <!-- c38r -->
    </div> <!-- subcolumns -->
</div> <!-- groups -->
