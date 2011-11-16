<?php /* Smarty version 2.6.26, created on 2011-11-16 00:10:54
         compiled from layout/base.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'layout/base.tpl', 4, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US" dir="ltr">
<head>
    <title><?php echo ((is_array($_tmp=@$this->_tpl_vars['title'])) ? $this->_run_mod_handler('default', true, $_tmp) : smarty_modifier_default($_tmp)); ?>
</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta http-equiv="Content-Script-Type" content="text/javascript" />
    <meta name="keywords" content="ucon, ann arbor, gaming convention, games, role-playing, collectable card games, ccgs, rpgs, rpga, magic, magic the gathering, auction, university of michigan, uofm, convention, miniatures, historicals, board games, card games" />
    <meta name="description" content="Affordable gaming convention in Ann Arbor, MI featuring a large variety of games, exhibitors hall and auction. Games include Role-playing, RPGA, CCGs, board, miniatures, historicals, card games." />
    <link rel="stylesheet" href="<?php echo $this->_tpl_vars['config']['page']['depth']; ?>
css/style.css" type="text/css"/>

    <link rel="SHORTCUT ICON" href="<?php echo $this->_tpl_vars['config']['page']['depth']; ?>
favicon.ico" />
    <script src="<?php echo $this->_tpl_vars['config']['page']['depth']; ?>
js/jquery-1.4.4.min.js" type="text/javascript" ></script>
    <script src="<?php echo $this->_tpl_vars['config']['page']['depth']; ?>
js/jquery.jeditable.js" type="text/javascript" ></script>
    <script src="<?php echo $this->_tpl_vars['config']['page']['depth']; ?>
js/jquery.jeditable.checkbox.js" type="text/javascript" ></script>
    </head>

<body><div class="mainpane" <?php if ($this->_tpl_vars['width']): ?>style="width:<?php echo $this->_tpl_vars['width']; ?>
px"<?php endif; ?>>

<div class="mainbar">

<table cellspacing="0" cellpadding="0">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "layout/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<tr>
<td class="sidebar"<?php if ($this->_tpl_vars['tabs']): ?> rowspan="2"<?php endif; ?>>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "layout/menu.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</td>
<?php echo ''; ?><?php if ($this->_tpl_vars['tabs']): ?><?php echo '<td class="tabs" colspan="2"><ul class="horizontal">'; ?><?php $_from = $this->_tpl_vars['tabs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?><?php echo '<li'; ?><?php if ($this->_tpl_vars['item']['link'] == $this->_tpl_vars['config']['page']['location']): ?><?php echo ' class="selected"'; ?><?php endif; ?><?php echo '><a href="'; ?><?php echo $this->_tpl_vars['config']['page']['depth']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['item']['link']; ?><?php echo ''; ?><?php echo $this->_tpl_vars['item']['querystring']; ?><?php echo '">'; ?><?php echo $this->_tpl_vars['item']['label']; ?><?php echo '</a></li>'; ?><?php endforeach; endif; unset($_from); ?><?php echo '</ul></td></tr><tr>'; ?><?php endif; ?><?php echo ''; ?>

<td class="content" colspan="2">
<!-- begin content -->
<?php echo $this->_tpl_vars['content']; ?>

<!-- end content -->
</td>

</tr></table></div>

<div class="footer">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "layout/footer.tpl", 'smarty_include_vars' => array('title' => 'User Info')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>

</div>

</body>
</html>