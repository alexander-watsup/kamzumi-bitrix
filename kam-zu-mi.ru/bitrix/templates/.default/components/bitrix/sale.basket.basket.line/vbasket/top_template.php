<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/**
 * @global array $arParams
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global string $cartId
 */
$compositeStub = (isset($arResult['COMPOSITE_STUB']) && $arResult['COMPOSITE_STUB'] == 'Y');
?><div class="bx-hdr-profile">
<?if (!$compositeStub && $arParams['SHOW_AUTHOR'] == 'Y'):?>
	<div class="bx-basket-block">
		<i class="fa fa-user"></i>
		<?if ($USER->IsAuthorized()):
			$name = trim($USER->GetFullName());
			if (! $name)
				$name = trim($USER->GetLogin());
			if (strlen($name) > 15)
				$name = substr($name, 0, 12).'...';
			?>
			<a href="<?=$arParams['PATH_TO_PROFILE']?>"><?=htmlspecialcharsbx($name)?></a>
			&nbsp;
			<a href="?logout=yes"><?=GetMessage('TSB1_LOGOUT')?></a>
		<?else:
			$arParamsToDelete = array(
				"login",
				"login_form",
				"logout",
				"register",
				"forgot_password",
				"change_password",
				"confirm_registration",
				"confirm_code",
				"confirm_user_id",
				"logout_butt",
				"auth_service_id",
				"clear_cache"
			);

			$currentUrl = urlencode($APPLICATION->GetCurPageParam("", $arParamsToDelete));
			if ($arParams['AJAX'] == 'N')
			{
				?><script type="text/javascript"><?=$cartId?>.currentUrl = '<?=$currentUrl?>';</script><?
			}
			else
			{
				$currentUrl = '#CURRENT_URL#';
			}
			
			$pathToAuthorize = $arParams['PATH_TO_AUTHORIZE'];
			$pathToAuthorize .= (stripos($pathToAuthorize, '?') === false ? '?' : '&');
			$pathToAuthorize .= 'login=yes&backurl='.$currentUrl;
			?>
			<a href="<?=$pathToAuthorize?>">
				<?=GetMessage('TSB1_LOGIN')?>
			</a>
			<?
			if ($arParams['SHOW_REGISTRATION'] === 'Y')
			{
				$pathToRegister = $arParams['PATH_TO_REGISTER'];
				$pathToRegister .= (stripos($pathToRegister, '?') === false ? '?' : '&');
				$pathToRegister .= 'register=yes&backurl='.$currentUrl;
				?>
				<a href="<?=$pathToRegister?>">
					<?=GetMessage('TSB1_REGISTER')?>
				</a>
				<?
			}
			?>
		<?endif?>
	</div>
<?endif?>
	<div class="vbasket-block">

		<?php ob_start(); ?>

		<div class="bx-basket-block"><?

			if ($arResult["USE_VBASKET"])
			{
				?><i class="fa fa-shopping-cart"></i>
				<a href="<?= $arParams['PATH_TO_BASKET'] ?>">
					<?=!empty($arResult['VBASKET_SELECTED']) ? $arResult['VBASKET_SELECTED']['NAME'] : GetMessage('TSB1_CART'); ?>
				</a><?
			}
			else
			{
				?><i class="fa fa-shopping-cart"></i>
				<a href="<?= $arParams['PATH_TO_BASKET'] ?>">
					<?=GetMessage('TSB1_CART'); ?>
				</a><?
			}

			if (!$compositeStub)
			{
				if ($arParams['SHOW_NUM_PRODUCTS'] == 'Y' && ($arResult['NUM_PRODUCTS'] > 0 || $arParams['SHOW_EMPTY_VALUES'] == 'Y'))
				{
					echo $arResult['NUM_PRODUCTS'].' '.$arResult['PRODUCT(S)'];

					if ($arParams['SHOW_TOTAL_PRICE'] == 'Y')
					{
						?>
						<br <? if ($arParams['POSITION_FIXED'] == 'Y'): ?>class="hidden-xs"<? endif; ?>/>
						<span>
							<?=GetMessage('TSB1_TOTAL_PRICE')?> <strong><?=$arResult['TOTAL_PRICE']?></strong>
						</span>
						<?
					}
				}
			}
			
		?></div><?php
		
		$content = ob_get_clean();
		echo $content;
		
		if ($arResult["USE_VBASKET"] && $arResult['VBASKET_NOT_SELECTED'])
		{
			?>

			<div class="vbasket-block__popup">
				<?php echo $content; ?>

				<div class="bx-basket-block">
					
					<div class="vbasket-list">
						<b><?=GetMessage('TSB1_SELECT_CART')?></b>

						<div class="vbasket-list__items">
						<?php foreach($arResult['VBASKET_NOT_SELECTED'] as $basket): if ($basket['SELECTED']) continue; ?>
							<a href="#" onclick="event.preventDefault(); VBasket.select('<?=$basket['CODE']?>')" class="vbasket-list__item">
								<span><?=$basket['NAME']?></span>
								<?php if (isset($basket['CNT'])): ?><b class="text-muted"><?=$basket['CNT']?></b><?php endif; ?>
							</a>
						<?php endforeach; ?>
						</div>
					</div>

				</div>
			</div><?php
		}
	?></div>
</div>