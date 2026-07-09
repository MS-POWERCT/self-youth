<?php

/**
 * A helper file for Dcat Admin, to provide autocomplete information to your IDE
 *
 * This file should not be included in your code, only analyzed by your IDE!
 *
 * @author jqh <841324345@qq.com>
 */
namespace Dcat\Admin {
    use Illuminate\Support\Collection;

    /**
     * @property Grid\Column|Collection batch_uuid
     * @property Grid\Column|Collection causer_id
     * @property Grid\Column|Collection causer_type
     * @property Grid\Column|Collection created_at
     * @property Grid\Column|Collection event
     * @property Grid\Column|Collection id
     * @property Grid\Column|Collection log_name
     * @property Grid\Column|Collection properties
     * @property Grid\Column|Collection subject_id
     * @property Grid\Column|Collection subject_type
     * @property Grid\Column|Collection updated_at
     * @property Grid\Column|Collection detail
     * @property Grid\Column|Collection name
     * @property Grid\Column|Collection type
     * @property Grid\Column|Collection version
     * @property Grid\Column|Collection is_enabled
     * @property Grid\Column|Collection extension
     * @property Grid\Column|Collection icon
     * @property Grid\Column|Collection order
     * @property Grid\Column|Collection parent_id
     * @property Grid\Column|Collection uri
     * @property Grid\Column|Collection menu_id
     * @property Grid\Column|Collection permission_id
     * @property Grid\Column|Collection http_method
     * @property Grid\Column|Collection http_path
     * @property Grid\Column|Collection slug
     * @property Grid\Column|Collection role_id
     * @property Grid\Column|Collection user_id
     * @property Grid\Column|Collection value
     * @property Grid\Column|Collection avatar
     * @property Grid\Column|Collection google_two_fa_enable
     * @property Grid\Column|Collection google_two_fa_msg
     * @property Grid\Column|Collection google_two_fa_secret
     * @property Grid\Column|Collection password
     * @property Grid\Column|Collection remember_token
     * @property Grid\Column|Collection status
     * @property Grid\Column|Collection username
     * @property Grid\Column|Collection img_url
     * @property Grid\Column|Collection position
     * @property Grid\Column|Collection sort
     * @property Grid\Column|Collection content
     * @property Grid\Column|Collection is_popup
     * @property Grid\Column|Collection jumpType
     * @property Grid\Column|Collection link
     * @property Grid\Column|Collection postion
     * @property Grid\Column|Collection admin_user_id
     * @property Grid\Column|Collection log
     * @property Grid\Column|Collection morph_id
     * @property Grid\Column|Collection morph_model
     * @property Grid\Column|Collection pre_create
     * @property Grid\Column|Collection connection
     * @property Grid\Column|Collection exception
     * @property Grid\Column|Collection failed_at
     * @property Grid\Column|Collection payload
     * @property Grid\Column|Collection queue
     * @property Grid\Column|Collection uuid
     * @property Grid\Column|Collection amount
     * @property Grid\Column|Collection asset_id
     * @property Grid\Column|Collection end_at
     * @property Grid\Column|Collection handbook_id
     * @property Grid\Column|Collection num
     * @property Grid\Column|Collection start_at
     * @property Grid\Column|Collection tool_id
     * @property Grid\Column|Collection desc
     * @property Grid\Column|Collection level_id
     * @property Grid\Column|Collection mature_after_time
     * @property Grid\Column|Collection mature_time
     * @property Grid\Column|Collection price
     * @property Grid\Column|Collection quarter
     * @property Grid\Column|Collection quarter_exp
     * @property Grid\Column|Collection quarter_output_num
     * @property Grid\Column|Collection selling_asset_id
     * @property Grid\Column|Collection selling_price
     * @property Grid\Column|Collection day_limit
     * @property Grid\Column|Collection npc_id
     * @property Grid\Column|Collection quality_ratio
     * @property Grid\Column|Collection quality_type
     * @property Grid\Column|Collection reward_asset_id
     * @property Grid\Column|Collection reward_exp
     * @property Grid\Column|Collection reward_gold
     * @property Grid\Column|Collection task_need
     * @property Grid\Column|Collection plant_mature_at
     * @property Grid\Column|Collection plant_start_at
     * @property Grid\Column|Collection residue_output
     * @property Grid\Column|Collection total_output
     * @property Grid\Column|Collection farm_task_id
     * @property Grid\Column|Collection ok_at
     * @property Grid\Column|Collection check_date
     * @property Grid\Column|Collection check_time
     * @property Grid\Column|Collection habit_id
     * @property Grid\Column|Collection date
     * @property Grid\Column|Collection total
     * @property Grid\Column|Collection deleted_at
     * @property Grid\Column|Collection note
     * @property Grid\Column|Collection note_image
     * @property Grid\Column|Collection record_date
     * @property Grid\Column|Collection record_end_time
     * @property Grid\Column|Collection record_start_time
     * @property Grid\Column|Collection images
     * @property Grid\Column|Collection circle_id
     * @property Grid\Column|Collection module_id
     * @property Grid\Column|Collection star
     * @property Grid\Column|Collection tags
     * @property Grid\Column|Collection category_id
     * @property Grid\Column|Collection item_id
     * @property Grid\Column|Collection mark_type
     * @property Grid\Column|Collection remark
     * @property Grid\Column|Collection client_id
     * @property Grid\Column|Collection expires_at
     * @property Grid\Column|Collection revoked
     * @property Grid\Column|Collection scopes
     * @property Grid\Column|Collection password_client
     * @property Grid\Column|Collection personal_access_client
     * @property Grid\Column|Collection provider
     * @property Grid\Column|Collection redirect
     * @property Grid\Column|Collection secret
     * @property Grid\Column|Collection access_token_id
     * @property Grid\Column|Collection appid
     * @property Grid\Column|Collection contents
     * @property Grid\Column|Collection create_date
     * @property Grid\Column|Collection create_env
     * @property Grid\Column|Collection is_mandatory
     * @property Grid\Column|Collection is_silently
     * @property Grid\Column|Collection min_uni_version
     * @property Grid\Column|Collection platform
     * @property Grid\Column|Collection stable_publish
     * @property Grid\Column|Collection uni_platform
     * @property Grid\Column|Collection url
     * @property Grid\Column|Collection email
     * @property Grid\Column|Collection token
     * @property Grid\Column|Collection abilities
     * @property Grid\Column|Collection last_used_at
     * @property Grid\Column|Collection tokenable_id
     * @property Grid\Column|Collection tokenable_type
     * @property Grid\Column|Collection command
     * @property Grid\Column|Collection cron
     * @property Grid\Column|Collection last_run_at
     * @property Grid\Column|Collection icon_id
     * @property Grid\Column|Collection fixed
     * @property Grid\Column|Collection is_show
     * @property Grid\Column|Collection address
     * @property Grid\Column|Collection age
     * @property Grid\Column|Collection birthday
     * @property Grid\Column|Collection gender
     * @property Grid\Column|Collection ip
     * @property Grid\Column|Collection last_online_at
     * @property Grid\Column|Collection login_type
     * @property Grid\Column|Collection signature
     * @property Grid\Column|Collection balance_change
     * @property Grid\Column|Collection module_code
     * @property Grid\Column|Collection wallet_asset_id
     * @property Grid\Column|Collection balance
     *
     * @method Grid\Column|Collection batch_uuid(string $label = null)
     * @method Grid\Column|Collection causer_id(string $label = null)
     * @method Grid\Column|Collection causer_type(string $label = null)
     * @method Grid\Column|Collection created_at(string $label = null)
     * @method Grid\Column|Collection event(string $label = null)
     * @method Grid\Column|Collection id(string $label = null)
     * @method Grid\Column|Collection log_name(string $label = null)
     * @method Grid\Column|Collection properties(string $label = null)
     * @method Grid\Column|Collection subject_id(string $label = null)
     * @method Grid\Column|Collection subject_type(string $label = null)
     * @method Grid\Column|Collection updated_at(string $label = null)
     * @method Grid\Column|Collection detail(string $label = null)
     * @method Grid\Column|Collection name(string $label = null)
     * @method Grid\Column|Collection type(string $label = null)
     * @method Grid\Column|Collection version(string $label = null)
     * @method Grid\Column|Collection is_enabled(string $label = null)
     * @method Grid\Column|Collection extension(string $label = null)
     * @method Grid\Column|Collection icon(string $label = null)
     * @method Grid\Column|Collection order(string $label = null)
     * @method Grid\Column|Collection parent_id(string $label = null)
     * @method Grid\Column|Collection uri(string $label = null)
     * @method Grid\Column|Collection menu_id(string $label = null)
     * @method Grid\Column|Collection permission_id(string $label = null)
     * @method Grid\Column|Collection http_method(string $label = null)
     * @method Grid\Column|Collection http_path(string $label = null)
     * @method Grid\Column|Collection slug(string $label = null)
     * @method Grid\Column|Collection role_id(string $label = null)
     * @method Grid\Column|Collection user_id(string $label = null)
     * @method Grid\Column|Collection value(string $label = null)
     * @method Grid\Column|Collection avatar(string $label = null)
     * @method Grid\Column|Collection google_two_fa_enable(string $label = null)
     * @method Grid\Column|Collection google_two_fa_msg(string $label = null)
     * @method Grid\Column|Collection google_two_fa_secret(string $label = null)
     * @method Grid\Column|Collection password(string $label = null)
     * @method Grid\Column|Collection remember_token(string $label = null)
     * @method Grid\Column|Collection status(string $label = null)
     * @method Grid\Column|Collection username(string $label = null)
     * @method Grid\Column|Collection img_url(string $label = null)
     * @method Grid\Column|Collection position(string $label = null)
     * @method Grid\Column|Collection sort(string $label = null)
     * @method Grid\Column|Collection content(string $label = null)
     * @method Grid\Column|Collection is_popup(string $label = null)
     * @method Grid\Column|Collection jumpType(string $label = null)
     * @method Grid\Column|Collection link(string $label = null)
     * @method Grid\Column|Collection postion(string $label = null)
     * @method Grid\Column|Collection admin_user_id(string $label = null)
     * @method Grid\Column|Collection log(string $label = null)
     * @method Grid\Column|Collection morph_id(string $label = null)
     * @method Grid\Column|Collection morph_model(string $label = null)
     * @method Grid\Column|Collection pre_create(string $label = null)
     * @method Grid\Column|Collection connection(string $label = null)
     * @method Grid\Column|Collection exception(string $label = null)
     * @method Grid\Column|Collection failed_at(string $label = null)
     * @method Grid\Column|Collection payload(string $label = null)
     * @method Grid\Column|Collection queue(string $label = null)
     * @method Grid\Column|Collection uuid(string $label = null)
     * @method Grid\Column|Collection amount(string $label = null)
     * @method Grid\Column|Collection asset_id(string $label = null)
     * @method Grid\Column|Collection end_at(string $label = null)
     * @method Grid\Column|Collection handbook_id(string $label = null)
     * @method Grid\Column|Collection num(string $label = null)
     * @method Grid\Column|Collection start_at(string $label = null)
     * @method Grid\Column|Collection tool_id(string $label = null)
     * @method Grid\Column|Collection desc(string $label = null)
     * @method Grid\Column|Collection level_id(string $label = null)
     * @method Grid\Column|Collection mature_after_time(string $label = null)
     * @method Grid\Column|Collection mature_time(string $label = null)
     * @method Grid\Column|Collection price(string $label = null)
     * @method Grid\Column|Collection quarter(string $label = null)
     * @method Grid\Column|Collection quarter_exp(string $label = null)
     * @method Grid\Column|Collection quarter_output_num(string $label = null)
     * @method Grid\Column|Collection selling_asset_id(string $label = null)
     * @method Grid\Column|Collection selling_price(string $label = null)
     * @method Grid\Column|Collection day_limit(string $label = null)
     * @method Grid\Column|Collection npc_id(string $label = null)
     * @method Grid\Column|Collection quality_ratio(string $label = null)
     * @method Grid\Column|Collection quality_type(string $label = null)
     * @method Grid\Column|Collection reward_asset_id(string $label = null)
     * @method Grid\Column|Collection reward_exp(string $label = null)
     * @method Grid\Column|Collection reward_gold(string $label = null)
     * @method Grid\Column|Collection task_need(string $label = null)
     * @method Grid\Column|Collection plant_mature_at(string $label = null)
     * @method Grid\Column|Collection plant_start_at(string $label = null)
     * @method Grid\Column|Collection residue_output(string $label = null)
     * @method Grid\Column|Collection total_output(string $label = null)
     * @method Grid\Column|Collection farm_task_id(string $label = null)
     * @method Grid\Column|Collection ok_at(string $label = null)
     * @method Grid\Column|Collection check_date(string $label = null)
     * @method Grid\Column|Collection check_time(string $label = null)
     * @method Grid\Column|Collection habit_id(string $label = null)
     * @method Grid\Column|Collection date(string $label = null)
     * @method Grid\Column|Collection total(string $label = null)
     * @method Grid\Column|Collection deleted_at(string $label = null)
     * @method Grid\Column|Collection note(string $label = null)
     * @method Grid\Column|Collection note_image(string $label = null)
     * @method Grid\Column|Collection record_date(string $label = null)
     * @method Grid\Column|Collection record_end_time(string $label = null)
     * @method Grid\Column|Collection record_start_time(string $label = null)
     * @method Grid\Column|Collection images(string $label = null)
     * @method Grid\Column|Collection circle_id(string $label = null)
     * @method Grid\Column|Collection module_id(string $label = null)
     * @method Grid\Column|Collection star(string $label = null)
     * @method Grid\Column|Collection tags(string $label = null)
     * @method Grid\Column|Collection category_id(string $label = null)
     * @method Grid\Column|Collection item_id(string $label = null)
     * @method Grid\Column|Collection mark_type(string $label = null)
     * @method Grid\Column|Collection remark(string $label = null)
     * @method Grid\Column|Collection client_id(string $label = null)
     * @method Grid\Column|Collection expires_at(string $label = null)
     * @method Grid\Column|Collection revoked(string $label = null)
     * @method Grid\Column|Collection scopes(string $label = null)
     * @method Grid\Column|Collection password_client(string $label = null)
     * @method Grid\Column|Collection personal_access_client(string $label = null)
     * @method Grid\Column|Collection provider(string $label = null)
     * @method Grid\Column|Collection redirect(string $label = null)
     * @method Grid\Column|Collection secret(string $label = null)
     * @method Grid\Column|Collection access_token_id(string $label = null)
     * @method Grid\Column|Collection appid(string $label = null)
     * @method Grid\Column|Collection contents(string $label = null)
     * @method Grid\Column|Collection create_date(string $label = null)
     * @method Grid\Column|Collection create_env(string $label = null)
     * @method Grid\Column|Collection is_mandatory(string $label = null)
     * @method Grid\Column|Collection is_silently(string $label = null)
     * @method Grid\Column|Collection min_uni_version(string $label = null)
     * @method Grid\Column|Collection platform(string $label = null)
     * @method Grid\Column|Collection stable_publish(string $label = null)
     * @method Grid\Column|Collection uni_platform(string $label = null)
     * @method Grid\Column|Collection url(string $label = null)
     * @method Grid\Column|Collection email(string $label = null)
     * @method Grid\Column|Collection token(string $label = null)
     * @method Grid\Column|Collection abilities(string $label = null)
     * @method Grid\Column|Collection last_used_at(string $label = null)
     * @method Grid\Column|Collection tokenable_id(string $label = null)
     * @method Grid\Column|Collection tokenable_type(string $label = null)
     * @method Grid\Column|Collection command(string $label = null)
     * @method Grid\Column|Collection cron(string $label = null)
     * @method Grid\Column|Collection last_run_at(string $label = null)
     * @method Grid\Column|Collection icon_id(string $label = null)
     * @method Grid\Column|Collection fixed(string $label = null)
     * @method Grid\Column|Collection is_show(string $label = null)
     * @method Grid\Column|Collection address(string $label = null)
     * @method Grid\Column|Collection age(string $label = null)
     * @method Grid\Column|Collection birthday(string $label = null)
     * @method Grid\Column|Collection gender(string $label = null)
     * @method Grid\Column|Collection ip(string $label = null)
     * @method Grid\Column|Collection last_online_at(string $label = null)
     * @method Grid\Column|Collection login_type(string $label = null)
     * @method Grid\Column|Collection signature(string $label = null)
     * @method Grid\Column|Collection balance_change(string $label = null)
     * @method Grid\Column|Collection module_code(string $label = null)
     * @method Grid\Column|Collection wallet_asset_id(string $label = null)
     * @method Grid\Column|Collection balance(string $label = null)
     */
    class Grid {}

    class MiniGrid extends Grid {}

    /**
     * @property Show\Field|Collection batch_uuid
     * @property Show\Field|Collection causer_id
     * @property Show\Field|Collection causer_type
     * @property Show\Field|Collection created_at
     * @property Show\Field|Collection event
     * @property Show\Field|Collection id
     * @property Show\Field|Collection log_name
     * @property Show\Field|Collection properties
     * @property Show\Field|Collection subject_id
     * @property Show\Field|Collection subject_type
     * @property Show\Field|Collection updated_at
     * @property Show\Field|Collection detail
     * @property Show\Field|Collection name
     * @property Show\Field|Collection type
     * @property Show\Field|Collection version
     * @property Show\Field|Collection is_enabled
     * @property Show\Field|Collection extension
     * @property Show\Field|Collection icon
     * @property Show\Field|Collection order
     * @property Show\Field|Collection parent_id
     * @property Show\Field|Collection uri
     * @property Show\Field|Collection menu_id
     * @property Show\Field|Collection permission_id
     * @property Show\Field|Collection http_method
     * @property Show\Field|Collection http_path
     * @property Show\Field|Collection slug
     * @property Show\Field|Collection role_id
     * @property Show\Field|Collection user_id
     * @property Show\Field|Collection value
     * @property Show\Field|Collection avatar
     * @property Show\Field|Collection google_two_fa_enable
     * @property Show\Field|Collection google_two_fa_msg
     * @property Show\Field|Collection google_two_fa_secret
     * @property Show\Field|Collection password
     * @property Show\Field|Collection remember_token
     * @property Show\Field|Collection status
     * @property Show\Field|Collection username
     * @property Show\Field|Collection img_url
     * @property Show\Field|Collection position
     * @property Show\Field|Collection sort
     * @property Show\Field|Collection content
     * @property Show\Field|Collection is_popup
     * @property Show\Field|Collection jumpType
     * @property Show\Field|Collection link
     * @property Show\Field|Collection postion
     * @property Show\Field|Collection admin_user_id
     * @property Show\Field|Collection log
     * @property Show\Field|Collection morph_id
     * @property Show\Field|Collection morph_model
     * @property Show\Field|Collection pre_create
     * @property Show\Field|Collection connection
     * @property Show\Field|Collection exception
     * @property Show\Field|Collection failed_at
     * @property Show\Field|Collection payload
     * @property Show\Field|Collection queue
     * @property Show\Field|Collection uuid
     * @property Show\Field|Collection amount
     * @property Show\Field|Collection asset_id
     * @property Show\Field|Collection end_at
     * @property Show\Field|Collection handbook_id
     * @property Show\Field|Collection num
     * @property Show\Field|Collection start_at
     * @property Show\Field|Collection tool_id
     * @property Show\Field|Collection desc
     * @property Show\Field|Collection level_id
     * @property Show\Field|Collection mature_after_time
     * @property Show\Field|Collection mature_time
     * @property Show\Field|Collection price
     * @property Show\Field|Collection quarter
     * @property Show\Field|Collection quarter_exp
     * @property Show\Field|Collection quarter_output_num
     * @property Show\Field|Collection selling_asset_id
     * @property Show\Field|Collection selling_price
     * @property Show\Field|Collection day_limit
     * @property Show\Field|Collection npc_id
     * @property Show\Field|Collection quality_ratio
     * @property Show\Field|Collection quality_type
     * @property Show\Field|Collection reward_asset_id
     * @property Show\Field|Collection reward_exp
     * @property Show\Field|Collection reward_gold
     * @property Show\Field|Collection task_need
     * @property Show\Field|Collection plant_mature_at
     * @property Show\Field|Collection plant_start_at
     * @property Show\Field|Collection residue_output
     * @property Show\Field|Collection total_output
     * @property Show\Field|Collection farm_task_id
     * @property Show\Field|Collection ok_at
     * @property Show\Field|Collection check_date
     * @property Show\Field|Collection check_time
     * @property Show\Field|Collection habit_id
     * @property Show\Field|Collection date
     * @property Show\Field|Collection total
     * @property Show\Field|Collection deleted_at
     * @property Show\Field|Collection note
     * @property Show\Field|Collection note_image
     * @property Show\Field|Collection record_date
     * @property Show\Field|Collection record_end_time
     * @property Show\Field|Collection record_start_time
     * @property Show\Field|Collection images
     * @property Show\Field|Collection circle_id
     * @property Show\Field|Collection module_id
     * @property Show\Field|Collection star
     * @property Show\Field|Collection tags
     * @property Show\Field|Collection category_id
     * @property Show\Field|Collection item_id
     * @property Show\Field|Collection mark_type
     * @property Show\Field|Collection remark
     * @property Show\Field|Collection client_id
     * @property Show\Field|Collection expires_at
     * @property Show\Field|Collection revoked
     * @property Show\Field|Collection scopes
     * @property Show\Field|Collection password_client
     * @property Show\Field|Collection personal_access_client
     * @property Show\Field|Collection provider
     * @property Show\Field|Collection redirect
     * @property Show\Field|Collection secret
     * @property Show\Field|Collection access_token_id
     * @property Show\Field|Collection appid
     * @property Show\Field|Collection contents
     * @property Show\Field|Collection create_date
     * @property Show\Field|Collection create_env
     * @property Show\Field|Collection is_mandatory
     * @property Show\Field|Collection is_silently
     * @property Show\Field|Collection min_uni_version
     * @property Show\Field|Collection platform
     * @property Show\Field|Collection stable_publish
     * @property Show\Field|Collection uni_platform
     * @property Show\Field|Collection url
     * @property Show\Field|Collection email
     * @property Show\Field|Collection token
     * @property Show\Field|Collection abilities
     * @property Show\Field|Collection last_used_at
     * @property Show\Field|Collection tokenable_id
     * @property Show\Field|Collection tokenable_type
     * @property Show\Field|Collection command
     * @property Show\Field|Collection cron
     * @property Show\Field|Collection last_run_at
     * @property Show\Field|Collection icon_id
     * @property Show\Field|Collection fixed
     * @property Show\Field|Collection is_show
     * @property Show\Field|Collection address
     * @property Show\Field|Collection age
     * @property Show\Field|Collection birthday
     * @property Show\Field|Collection gender
     * @property Show\Field|Collection ip
     * @property Show\Field|Collection last_online_at
     * @property Show\Field|Collection login_type
     * @property Show\Field|Collection signature
     * @property Show\Field|Collection balance_change
     * @property Show\Field|Collection module_code
     * @property Show\Field|Collection wallet_asset_id
     * @property Show\Field|Collection balance
     *
     * @method Show\Field|Collection batch_uuid(string $label = null)
     * @method Show\Field|Collection causer_id(string $label = null)
     * @method Show\Field|Collection causer_type(string $label = null)
     * @method Show\Field|Collection created_at(string $label = null)
     * @method Show\Field|Collection event(string $label = null)
     * @method Show\Field|Collection id(string $label = null)
     * @method Show\Field|Collection log_name(string $label = null)
     * @method Show\Field|Collection properties(string $label = null)
     * @method Show\Field|Collection subject_id(string $label = null)
     * @method Show\Field|Collection subject_type(string $label = null)
     * @method Show\Field|Collection updated_at(string $label = null)
     * @method Show\Field|Collection detail(string $label = null)
     * @method Show\Field|Collection name(string $label = null)
     * @method Show\Field|Collection type(string $label = null)
     * @method Show\Field|Collection version(string $label = null)
     * @method Show\Field|Collection is_enabled(string $label = null)
     * @method Show\Field|Collection extension(string $label = null)
     * @method Show\Field|Collection icon(string $label = null)
     * @method Show\Field|Collection order(string $label = null)
     * @method Show\Field|Collection parent_id(string $label = null)
     * @method Show\Field|Collection uri(string $label = null)
     * @method Show\Field|Collection menu_id(string $label = null)
     * @method Show\Field|Collection permission_id(string $label = null)
     * @method Show\Field|Collection http_method(string $label = null)
     * @method Show\Field|Collection http_path(string $label = null)
     * @method Show\Field|Collection slug(string $label = null)
     * @method Show\Field|Collection role_id(string $label = null)
     * @method Show\Field|Collection user_id(string $label = null)
     * @method Show\Field|Collection value(string $label = null)
     * @method Show\Field|Collection avatar(string $label = null)
     * @method Show\Field|Collection google_two_fa_enable(string $label = null)
     * @method Show\Field|Collection google_two_fa_msg(string $label = null)
     * @method Show\Field|Collection google_two_fa_secret(string $label = null)
     * @method Show\Field|Collection password(string $label = null)
     * @method Show\Field|Collection remember_token(string $label = null)
     * @method Show\Field|Collection status(string $label = null)
     * @method Show\Field|Collection username(string $label = null)
     * @method Show\Field|Collection img_url(string $label = null)
     * @method Show\Field|Collection position(string $label = null)
     * @method Show\Field|Collection sort(string $label = null)
     * @method Show\Field|Collection content(string $label = null)
     * @method Show\Field|Collection is_popup(string $label = null)
     * @method Show\Field|Collection jumpType(string $label = null)
     * @method Show\Field|Collection link(string $label = null)
     * @method Show\Field|Collection postion(string $label = null)
     * @method Show\Field|Collection admin_user_id(string $label = null)
     * @method Show\Field|Collection log(string $label = null)
     * @method Show\Field|Collection morph_id(string $label = null)
     * @method Show\Field|Collection morph_model(string $label = null)
     * @method Show\Field|Collection pre_create(string $label = null)
     * @method Show\Field|Collection connection(string $label = null)
     * @method Show\Field|Collection exception(string $label = null)
     * @method Show\Field|Collection failed_at(string $label = null)
     * @method Show\Field|Collection payload(string $label = null)
     * @method Show\Field|Collection queue(string $label = null)
     * @method Show\Field|Collection uuid(string $label = null)
     * @method Show\Field|Collection amount(string $label = null)
     * @method Show\Field|Collection asset_id(string $label = null)
     * @method Show\Field|Collection end_at(string $label = null)
     * @method Show\Field|Collection handbook_id(string $label = null)
     * @method Show\Field|Collection num(string $label = null)
     * @method Show\Field|Collection start_at(string $label = null)
     * @method Show\Field|Collection tool_id(string $label = null)
     * @method Show\Field|Collection desc(string $label = null)
     * @method Show\Field|Collection level_id(string $label = null)
     * @method Show\Field|Collection mature_after_time(string $label = null)
     * @method Show\Field|Collection mature_time(string $label = null)
     * @method Show\Field|Collection price(string $label = null)
     * @method Show\Field|Collection quarter(string $label = null)
     * @method Show\Field|Collection quarter_exp(string $label = null)
     * @method Show\Field|Collection quarter_output_num(string $label = null)
     * @method Show\Field|Collection selling_asset_id(string $label = null)
     * @method Show\Field|Collection selling_price(string $label = null)
     * @method Show\Field|Collection day_limit(string $label = null)
     * @method Show\Field|Collection npc_id(string $label = null)
     * @method Show\Field|Collection quality_ratio(string $label = null)
     * @method Show\Field|Collection quality_type(string $label = null)
     * @method Show\Field|Collection reward_asset_id(string $label = null)
     * @method Show\Field|Collection reward_exp(string $label = null)
     * @method Show\Field|Collection reward_gold(string $label = null)
     * @method Show\Field|Collection task_need(string $label = null)
     * @method Show\Field|Collection plant_mature_at(string $label = null)
     * @method Show\Field|Collection plant_start_at(string $label = null)
     * @method Show\Field|Collection residue_output(string $label = null)
     * @method Show\Field|Collection total_output(string $label = null)
     * @method Show\Field|Collection farm_task_id(string $label = null)
     * @method Show\Field|Collection ok_at(string $label = null)
     * @method Show\Field|Collection check_date(string $label = null)
     * @method Show\Field|Collection check_time(string $label = null)
     * @method Show\Field|Collection habit_id(string $label = null)
     * @method Show\Field|Collection date(string $label = null)
     * @method Show\Field|Collection total(string $label = null)
     * @method Show\Field|Collection deleted_at(string $label = null)
     * @method Show\Field|Collection note(string $label = null)
     * @method Show\Field|Collection note_image(string $label = null)
     * @method Show\Field|Collection record_date(string $label = null)
     * @method Show\Field|Collection record_end_time(string $label = null)
     * @method Show\Field|Collection record_start_time(string $label = null)
     * @method Show\Field|Collection images(string $label = null)
     * @method Show\Field|Collection circle_id(string $label = null)
     * @method Show\Field|Collection module_id(string $label = null)
     * @method Show\Field|Collection star(string $label = null)
     * @method Show\Field|Collection tags(string $label = null)
     * @method Show\Field|Collection category_id(string $label = null)
     * @method Show\Field|Collection item_id(string $label = null)
     * @method Show\Field|Collection mark_type(string $label = null)
     * @method Show\Field|Collection remark(string $label = null)
     * @method Show\Field|Collection client_id(string $label = null)
     * @method Show\Field|Collection expires_at(string $label = null)
     * @method Show\Field|Collection revoked(string $label = null)
     * @method Show\Field|Collection scopes(string $label = null)
     * @method Show\Field|Collection password_client(string $label = null)
     * @method Show\Field|Collection personal_access_client(string $label = null)
     * @method Show\Field|Collection provider(string $label = null)
     * @method Show\Field|Collection redirect(string $label = null)
     * @method Show\Field|Collection secret(string $label = null)
     * @method Show\Field|Collection access_token_id(string $label = null)
     * @method Show\Field|Collection appid(string $label = null)
     * @method Show\Field|Collection contents(string $label = null)
     * @method Show\Field|Collection create_date(string $label = null)
     * @method Show\Field|Collection create_env(string $label = null)
     * @method Show\Field|Collection is_mandatory(string $label = null)
     * @method Show\Field|Collection is_silently(string $label = null)
     * @method Show\Field|Collection min_uni_version(string $label = null)
     * @method Show\Field|Collection platform(string $label = null)
     * @method Show\Field|Collection stable_publish(string $label = null)
     * @method Show\Field|Collection uni_platform(string $label = null)
     * @method Show\Field|Collection url(string $label = null)
     * @method Show\Field|Collection email(string $label = null)
     * @method Show\Field|Collection token(string $label = null)
     * @method Show\Field|Collection abilities(string $label = null)
     * @method Show\Field|Collection last_used_at(string $label = null)
     * @method Show\Field|Collection tokenable_id(string $label = null)
     * @method Show\Field|Collection tokenable_type(string $label = null)
     * @method Show\Field|Collection command(string $label = null)
     * @method Show\Field|Collection cron(string $label = null)
     * @method Show\Field|Collection last_run_at(string $label = null)
     * @method Show\Field|Collection icon_id(string $label = null)
     * @method Show\Field|Collection fixed(string $label = null)
     * @method Show\Field|Collection is_show(string $label = null)
     * @method Show\Field|Collection address(string $label = null)
     * @method Show\Field|Collection age(string $label = null)
     * @method Show\Field|Collection birthday(string $label = null)
     * @method Show\Field|Collection gender(string $label = null)
     * @method Show\Field|Collection ip(string $label = null)
     * @method Show\Field|Collection last_online_at(string $label = null)
     * @method Show\Field|Collection login_type(string $label = null)
     * @method Show\Field|Collection signature(string $label = null)
     * @method Show\Field|Collection balance_change(string $label = null)
     * @method Show\Field|Collection module_code(string $label = null)
     * @method Show\Field|Collection wallet_asset_id(string $label = null)
     * @method Show\Field|Collection balance(string $label = null)
     */
    class Show {}

    /**
     
     */
    class Form {}

}

namespace Dcat\Admin\Grid {
    /**
     * @method $this datetimeSplit(...$params)
     */
    class Column {}

    /**
     
     */
    class Filter {}
}

namespace Dcat\Admin\Show {
    /**
     
     */
    class Field {}
}
