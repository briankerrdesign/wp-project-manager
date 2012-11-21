<?php

/**
 * A logging class for tracking activity
 *
 * @author Tareq Hasan
 */
class CPM_Activity {

    function __construct() {
        //project
        add_action( 'cpm_project_new', array($this, 'project_new') );
        add_action( 'cpm_project_update', array($this, 'project_update') );

        //message
        add_action( 'cpm_message_new', array($this, 'message_new'), 10, 2 );
        add_action( 'cpm_message_update', array($this, 'message_update'), 10, 2 );
        add_action( 'cpm_message_delete', array($this, 'message_delete') );

        //to-do list
        add_action( 'cpm_tasklist_new', array($this, 'tasklist_new'), 10, 2 );
        add_action( 'cpm_tasklist_update', array($this, 'tasklist_update'), 10, 2 );
        add_action( 'cpm_tasklist_delete', array($this, 'tasklist_delete') );

        //to-do
        add_action( 'cpm_task_new', array($this, 'task_new'), 10, 2 );
        add_action( 'cpm_task_update', array($this, 'task_update'), 10, 2 );
        add_action( 'cpm_task_complete', array($this, 'task_done') );
        add_action( 'cpm_task_open', array($this, 'task_undo') );
        add_action( 'cpm_task_delete', array($this, 'task_delete') );

        //comment
        add_action( 'cpm_comment_new', array($this, 'comment_new'), 10, 2 );
        add_action( 'cpm_comment_update', array($this, 'comment_update'), 10, 2 );
        add_action( 'cpm_comment_delete', array($this, 'comment_delete') );

        //milestone
        add_action( 'cpm_milestone_new', array($this, 'milestone_new'), 10, 2 );
        add_action( 'cpm_milestone_update', array($this, 'milestone_update'), 10, 2 );
        add_action( 'cpm_milestone_delete', array($this, 'milestone_delete') );
        add_action( 'cpm_milestone_complete', array($this, 'milestone_done') );
        add_action( 'cpm_milestone_open', array($this, 'milestone_open') );
    }

    function user_url() {
        return sprintf( '[cpm_user_url id="%d"]', get_current_user_id() );
    }

    function message_url( $message_id, $project_id, $title ) {
        return sprintf( '[cpm_msg_url id="%d" project="%d" title="%s"]', $message_id, $project_id, $title );
    }

    function list_url( $list_id, $project_id, $title ) {
        return sprintf( '[cpm_tasklist_url id="%d" project="%d" title="%s"]', $list_id, $project_id, $title );
    }

    function task_url( $task_id, $list_id, $project_id, $title ) {
        return sprintf( '[cpm_task_url id="%d" project="%d" list="%d" title="%s"]', $task_id, $list_id, $project_id, $title );
    }

    function project_new( $project_id ) {
        $message = sprintf( __( 'Project created by %s', 'cpm' ), $this->user_url() );

        $this->log( $project_id, $message );
    }

    function project_update( $project_id ) {
        $message = sprintf( __( 'Project details updated by %s', 'cpm' ), $this->user_url() );

        $this->log( $project_id, $message );
    }

    function message_new( $message_id, $project_id ) {
        $msg = get_post( $message_id );
        $message = sprintf( 
            __( 'Message %s created by %s', 'cpm' ), 
            $this->message_url( $message_id, $project_id, $msg->post_title ), 
            $this->user_url()
        );

        $this->log( $project_id, $message );
    }

    function message_update( $message_id, $project_id ) {
        $msg = get_post( $message_id );
        $message = sprintf( 
            __( 'Message %s updated by %s', 'cpm' ), 
            $this->message_url( $message_id, $project_id, $msg->post_title ), 
            $this->user_url()
        );

        $this->log( $project_id, $message );
    }

    function message_delete( $message_id ) {
        $msg = get_post( $message_id );
        $message = sprintf( __( 'Message "%s" deleted by %s', 'cpm' ), $msg->post_title, $this->user_url() );

        $this->log( $msg->post_parent, $message );
    }

    function tasklist_new( $list_id, $project_id ) {
        $list = get_post( $list_id );
        $message = sprintf( 
            __( 'To-do list %s created by %s', 'cpm' ), 
            $this->list_url( $list_id, $project_id, $list->post_title ), 
            $this->user_url()
        );

        $this->log( $project_id, $message );
    }

    function tasklist_update( $list_id, $project_id ) {
        $list = get_post( $list_id );
        $message = sprintf( 
            __( 'To-do list %s updated by %s', 'cpm' ), 
            $this->list_url( $list_id, $project_id, $list->post_title ), 
            $this->user_url()
        );

        $this->log( $project_id, $message );
    }

    function tasklist_delete( $list_id ) {
        $list = get_post( $list_id );
        $message = sprintf( 
            __( 'To-do list "%s" deleted by %s', 'cpm' ), 
            $list->post_title, 
            $this->user_url()
        );

        $this->log( $list->post_parent, $message );
    }

    function task_new( $list_id, $task_id ) {
        $list = get_post( $list_id );
        $task = get_post( $task_id );

        $message = sprintf(
            __( 'To-do %s added on to-do list %s by %s', 'cpm' ), 
            $this->task_url( $task_id, $list_id, $list->post_parent, $task->post_title ), 
            $this->list_url( $list_id, $list->post_parent, $list->post_title ), 
            $this->user_url()
        );

        $this->log( $list->post_parent, $message );
    }

    function task_update( $list_id, $task_id ) {
        $list = get_post( $list_id );
        $task = get_post( $task_id );

        $message = sprintf(
            __( 'To-do %s updated by %s', 'cpm' ), 
            $this->task_url( $task_id, $list_id, $list->post_parent, $task->post_title ), 
            $this->user_url()
        );

        $this->log( $list->post_parent, $message );
    }

    function task_done( $task_id ) {
        $task = get_post( $task_id );
        $list = get_post( $task->post_parent );

        $message = sprintf(
            __( 'To-do %s completed by %s', 'cpm' ), 
            $this->task_url( $task_id, $list->ID, $list->post_parent, $task->post_title ), 
            $this->user_url()
        );

        $task_message = sprintf( __( 'Marked to-do as done', 'cpm' ) );

        $this->log( $list->post_parent, $message );
        $this->log( $task_id, $task_message );
    }

    function task_undo( $task_id ) {
        $task = get_post( $task_id );
        $list = get_post( $task->post_parent );

        $message = sprintf(
            __( 'To-do %s marked un-done by %s', 'cpm' ), 
            $this->task_url( $task_id, $list->ID, $list->post_parent, $task->post_title ), 
            $this->user_url()
        );

        $task_message = sprintf( __( 'Re-opened to-do', 'cpm' ) );

        $this->log( $list->post_parent, $message );
        $this->log( $task_id, $task_message );
    }

    function task_delete( $task_id ) {
        $task = get_post( $task_id );
        $list = get_post( $task->post_parent );

        $message = sprintf(
            __( 'To-do "%s" deleted from to-do list %s by %s', 'cpm' ), 
            $task->post_title, 
            $this->list_url( $list->ID, $list->post_parent, $list->post_title ), 
            $this->user_url()
        );

        $this->log( $list->post_parent, $message );
    }

    function comment_new( $comment_id, $project_id ) {
        $message = sprintf( __( '%s commented on a %s', 'cpm' ), $this->user_url(), "[cpm_comment_url id='$comment_id' project='$project_id']" );

        $this->log( $project_id, $message );
    }

    function comment_update( $comment_id, $project_id ) {
        $message = sprintf( __( '%s updated comment on a %s', 'cpm' ), $this->user_url(), "[cpm_comment_url id='$comment_id' project='$project_id']" );

        $this->log( $project_id, $message );
    }

    function comment_delete( $comment_id ) {
        $comment = get_comment( $comment_id );

        $message = sprintf( __( '%s deleted a comment', 'cpm' ), $this->user_url() );

        $this->log( $_POST['project_id'], $message );
    }

    function milestone_new( $milestone_id, $project_id ) {
        $milestone = get_post( $milestone_id );
        $message = sprintf( __( 'Milestone "%s" added by %s ', 'wedevs' ), $milestone->post_title, $this->user_url() );

        $this->log( $project_id, $message );
    }

    function milestone_update( $milestone_id, $project_id ) {
        $milestone = get_post( $milestone_id );
        $message = sprintf( __( 'Milestone "%s" updated by %s ', 'wedevs' ), $milestone->post_title, $this->user_url() );

        $this->log( $project_id, $message );
    }

    function milestone_delete( $milestone_id ) {
        $milestone = get_post( $milestone_id );
        $message = sprintf( __( 'Milestone "%s" deleted by %s ', 'wedevs' ), $milestone->post_title, $this->user_url() );

        $this->log( $_POST['project_id'], $message );
    }

    function milestone_done( $milestone_id ) {
        $milestone = get_post( $milestone_id );
        $message = sprintf( __( 'Milestone "%s" marked as complete by %s ', 'wedevs' ), $milestone->post_title, $this->user_url() );

        $this->log( $_POST['project_id'], $message );
    }

    function milestone_open( $milestone_id ) {
        $milestone = get_post( $milestone_id );
        $message = sprintf( __( 'Milestone "%s" marked as incomplete by %s ', 'wedevs' ), $milestone->post_title, $this->user_url() );

        $this->log( $_POST['project_id'], $message );
    }

    function log( $post_id, $message ) {
        $user = wp_get_current_user();

        $commentdata = array(
            'comment_author_IP' => preg_replace( '/[^0-9a-fA-F:., ]/', '', $_SERVER['REMOTE_ADDR'] ),
            'comment_agent' => substr( $_SERVER['HTTP_USER_AGENT'], 0, 254 ),
            'comment_type' => 'cpm_activity',
            'comment_content' => $message,
            'comment_post_ID' => $post_id,
            'user_id' => $user->ID,
            'comment_author' => $user->display_name,
            'comment_author_email' => $user->user_email,
        );

        wp_insert_comment( $commentdata );
    }

}