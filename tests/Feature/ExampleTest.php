<?php

test('the login page returns a successful response', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});
