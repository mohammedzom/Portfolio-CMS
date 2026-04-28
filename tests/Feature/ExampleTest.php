<?php

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(302)->assertRedirect('https://mohammedzomlot.dev');
});
