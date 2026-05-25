<?php

it('returns default avatar when user is null', function () {
    expect(user_avatar(null))->toBe(asset('images/default-avatar.png'));
});

it('returns default avatar when user has no avatar', function () {
    $user = (object) ['avatar' => null];

    expect(user_avatar($user))->toBe(asset('images/default-avatar.png'));
});

it('returns default avatar when user avatar is empty string', function () {
    $user = (object) ['avatar' => ''];

    expect(user_avatar($user))->toBe(asset('images/default-avatar.png'));
});

it('returns external avatar URL untouched', function () {
    $user = (object) ['avatar' => 'https://cdn.example.com/me.jpg'];

    expect(user_avatar($user))->toBe('https://cdn.example.com/me.jpg');
});

it('returns asset URL for storage/-prefixed avatar', function () {
    $user = (object) ['avatar' => 'storage/avatars/u1.png'];

    expect(user_avatar($user))->toBe(asset('storage/avatars/u1.png'));
});

it('returns Storage::url() for plain storage path avatar', function () {
    $user = (object) ['avatar' => 'avatars/u1.png'];

    expect(user_avatar($user))->toBe(\Storage::url('avatars/u1.png'));
});
