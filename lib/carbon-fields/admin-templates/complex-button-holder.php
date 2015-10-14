<div class="carbon-btn-holder">
	<div class="carbon-complex-action">
		<?php if ( count($fields) > 1 && $this->layout == self::LAYOUT_TABLE ): ?>
			<a class="carbon-btn-collapse" href="#" data-action="toggle-minimize" title="<?php esc_attr_e('Collapse/Expand', 'crb'); ?>"><?php _e('Collapse/Expand', 'crb'); ?></a>
		<?php endif ?>
		<a class="carbon-btn-duplicate" href="#" data-action="duplicate" title="<?php esc_attr_e('Clonar', 'crb'); ?>"><?php _e('Clonar', 'crb'); ?></a>
		<a class="carbon-btn-remove" href="#" data-action="remove" title="<?php esc_attr_e('Borrar', 'crb'); ?>"><?php _e('Borrar', 'crb'); ?></a>
	</div>
</div>
