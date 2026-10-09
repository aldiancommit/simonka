<?php

test('the application returns a successful response', function () {
    $this->actingAsRole();

    $response = $this->get('/');

    $response->assertStatus(200);
});
