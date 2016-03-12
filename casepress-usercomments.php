<?php
/**
 * @package casepress-usercomments
 * @version 1.0
 */
/*
Plugin Name: casepress-usercomments
Plugin URI: -
Author: Petro-1
Version: 1.0
*/


$meta_type = 'post';
add_action("added_{$meta_type}_meta", 'cp_addmeta_usercomment', 10, 4 ); //Срабатывает при создании меты
add_action('update_postmeta', 'cp_changemeta_usercomment', 10, 4); //Срабатывает перед изменении меты, можно сравнить имеющийся id меты с изменяемым

function cp_addmeta_usercomment($mid, $object_id, $meta_key, $_meta_value) {
    $current_meta = get_post_meta($object_id, $meta_key, 1);
    if ($meta_key == 'responsible-cp-posts-sql' && $_meta_value != '') { //если добавляется мета Ответственный, то добавляем комментарий
        if ($user_id = get_user_by_person( $_meta_value )) { // если можно получить id пользователя,
            // то выводим в текст коммента ссылку на пользователя и логин
            $user_info = get_userdata($user_id);
            $profile_link = add_query_arg('user_id', $user_id, self_admin_url('user-edit.php'));
            $login = '@' . $user_info->user_login;
        } else { //иначе выводим имя персоны и ссылку на персону
            $login = '<a href="' . get_permalink($_meta_value) . '">' . get_the_title($_meta_value) . '</a>';
        }
        $data = array(
            'comment_post_ID'      => $object_id,
            'comment_content'      => $login . ' назначен ответственным по делу.',
            'user_id'              => get_current_user_id()
        );
        wp_insert_comment( $data ); //вставляем комментарий
    }
}

function cp_changemeta_usercomment($meta_id, $object_id, $meta_key, $meta_value) {
    if ($meta_key == 'responsible-cp-posts-sql') { //если изменяется мета Ответственный, то добавляем комментарий
        $current_meta = get_post_meta($object_id, $meta_key, 1); //Получаем значение меты до изменения
        if ($current_meta != $meta_value && $meta_value != '') { //если значение меняется, то добавляем комментарий
            if ($user_id = get_user_by_person( $meta_value )) { // если можно получить id пользователя,
                // то выводим в текст коммента ссылку на пользователя и логин
                $user_info = get_userdata($user_id);
                $login = '@' . $user_info->user_login;
            } else { //иначе выводим имя персоны и ссылку на персону
                $login = '<a href="' . get_permalink($meta_value) . '">' . get_the_title($meta_value) . '</a>';
            }
            $data = array(
                'comment_post_ID'      => $object_id,
                'comment_content'      => $login . ' назначен ответственным по делу.',
                'user_id'              => get_current_user_id()
            );
            wp_insert_comment( $data ); //вставляем комментарий
        }
    }
}
