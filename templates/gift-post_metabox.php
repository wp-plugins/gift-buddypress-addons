<table> 
    <tr valign="top">
        <th class="metabox_label_column">
            <label for="price">Price</label>
        </th>
        <td>
            <input type="text" id="price" name="price" value="<?php echo @get_post_meta($post->ID, 'price', true); ?>" />
        </td>
    </tr>
    <tr valign="top">
        <th class="metabox_label_column">
            <label for="price">Meta B</label>
        </th>
        <td>
            <input type="text" id="meta_b" name="meta_b" value="<?php echo @get_post_meta($post->ID, 'meta_b', true); ?>" />
        </td>
    </tr>
    <tr valign="top">
        <th class="metabox_label_column">
            <label for="price">Meta C</label>
        </th>
        <td>
            <input type="text" id="meta_c" name="meta_c" value="<?php echo @get_post_meta($post->ID, 'meta_c', true); ?>" />
        </td>
    </tr>                
</table>
