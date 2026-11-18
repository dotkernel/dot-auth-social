# Upgrading from 1.x to 2.x

Version 2 drops PHP 8.2, removes a dependency and changes some public signatures.
Find each change that affects your code in the table, then apply the fix.

| Change | Symptom | Fix |
| --- | --- | --- |
| PHP 8.2 is not supported | Composer refuses to install version 2 | Use PHP 8.3 or later |
| `laminas/laminas-servicemanager` is not a requirement | `Class "Laminas\ServiceManager\ServiceManager" not found` after `composer update` | Add `laminas/laminas-servicemanager` to the `composer.json` file of your application |
| `AuthenticationServiceInterface::getState()` is new | Fatal error in a class that implements the interface | Add `getState(): string` to your class |
| `AuthenticationResultInterface` has `getFirstName()`, `getLastName()` and `getFullName()` | Fatal error in a class that implements the interface | Add the three methods to your class |
| `AuthenticationResult::failed()` requires a string | `TypeError` when you pass `null` | Pass at least one message |
| `getExpiresAt()` returns `?DateTimeImmutable`, not `DateTimeImmutable\|false` | A check for `=== false` does not find a missing expiry time | Check for `=== null` |
| `expires_at` in `getArrayCopy()` is in the ATOM format, not `Y-m-d H:i:s` | Date parsing with `Y-m-d H:i:s` fails | Parse the ATOM format, or use `getExpiresAt()` |
| The constructor parameter `$expiresIn` of `AuthenticationResult` is now `$expires` | `Error: Unknown named parameter $expiresIn` | Use the named argument `expires:` |
| `AuthenticationResult::success()` accepts `null` for the names and the expiry time | None for callers. A subclass that overrides `success()` gets a signature error | Change the signature of your override |
| `getFullName()` returns `null` when the user has no name | Code that expects an empty string gets `null` | Check for `null` |
| Errors that do not come from Facebook have a generic message | A comparison with the old message text fails | Use `isValid()`, or compare with `FacebookService::MESSAGE_FAILED` |
| A user without an email address gets an error | The message is now `FacebookService::MESSAGE_NO_EMAIL`, not a PHP type error | Show the message to the user |
| The authorization URL uses the default Facebook scopes `public_profile` and `email` | The consent screen of Facebook asks for the public profile too | No change is necessary |

## Check the OAuth state

Version 1 did not give your application the OAuth `state` value.
Read [Usage](usage.md) and add the state check to your callback handler.
Without the check, your login is open to cross-site request forgery.
