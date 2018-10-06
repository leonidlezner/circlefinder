<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        'App\Circle' => 'App\Policies\CirclePolicy',
        'App\Membership' => 'App\Policies\MembershipPolicy',
        'App\Message' => 'App\Policies\MessagesPolicy',
        'App\PrivateMessage' => 'App\Policies\PrivateMessagesPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
