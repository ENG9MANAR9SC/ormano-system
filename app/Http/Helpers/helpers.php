<?php


use Illuminate\Contracts\Auth\Authenticatable;


function getCachePrefix()
{
	// TODO: get this from env file or config
	return 'mgr_store';
}
function getSessionPrefix()
{
	// TODO: get this from env file or config
	return 'mgr_store_';
}

/**
 * return current login user
 * @return Authenticatable|null
 */
function getAuthUser(): ?Authenticatable
{
	// TODO: change it to Auth::user()
	if (auth()->check() && $user = auth()->user()) {
		return $user;
	}
	return null;
}

function getNoAvailableImageUrl($id = null, $type = null): string
{
	## product model ##
	return 'https://mgr.caffeine-store.com' . '/assets/img/caffeine_no_image.png';

}


