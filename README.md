Setup
----

1. Install with composer.
2. Define which policies to load in your `AuthServiceProvider` (see below).

Abilities' names will follow method names:

- `function manageAllUsers()` becomes ability `manage-all-users`
- `function see360DegreeFeedback()` becomes ability `see360-degrees-feedback`
- `function see_360DegreeFeedback()` becomes ability `see-360-degrees-feedback`

----

Replace your `AuthServiceProvider` with this:

```
use App\Policies;
use rdx\authgate\PoliciesServiceProvider;

class AuthServiceProvider extends PoliciesServiceProvider {

	protected $policies = [
		Policies\UserPolicy::class,
		Policies\FilePolicy::class,
		// All your policy classes
	];

}
```

If you want to add more `register()` or `boot()` code, be sure to call `parent::register()` or `parent::boot()`!

See `PoliciesServiceProvider` for more options.
