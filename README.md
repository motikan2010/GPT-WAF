# GPT WAF for Laravel

This is a WAF (Web Application Firewall) using GPT-4. (Laravel only)

**Requirement : OpenAI API key for GPT-4.**

## Install

```
composer require motikan2010/gpt-waf
```

## Performance

| Status | Response Speed |
|-|-|
| **Disable** GPT WAF | 00.68 Sec 🚗 |
| **Enable** GPT WAF | 19.04 Sec 🐢 |

## Using

### Edit routing & ".env" file

Set "`gpt-waf`" as the middleware for routing.

```php
Route::group(['middleware' => 'gpt-waf'], function () {
  // Protect area
});

```

The following changes will be enabled.  
https://github.com/motikan2010/GPT-WAF-Test-App/commit/5a021e632488a585fc17d6360e2a89cd99a00eb6

### Setting of ".env"

#### Required

| Param | Description |
| - | - |
| `GPT_WAF_ENABLED` | GPT-WAF enabled or disabled. |
| `GPT_WAF_OPEN_AI_API_KEY` | OpenAI API key for GPT-4. |

ex.
```
GPT_WAF_ENABLED=true
GPT_WAF_OPEN_AI_API_KEY=st-tGC**********
```

#### Option

| Param | Description |
| - | - |
| `GPT_WAF_BLOCK_STATUS_CODE` | Status code at block.<br>(default: `403`) |
| `GPT_WAF_DEBUG_MODE` | Output "question" and "answer" to log.<br>(default: `false`) |
| `GPT_WAF_QUESTION_TEXT` | Beginning of message when asking a question to GPT.<br>By default, the question is asked with the following text.<br>`Begin your answer with 'Yes' or 'No'.\nIs the following HTTP request a cyber attack?(Beware of false positives.)\n-----` |
| `GPT_WAF_SYSTEM_ROLE` | GPT role(system). <br>(default: `You are Security engineer.`) |

ex.
```
GPT_WAF_BLOCK_STATUS_CODE=403
GPT_WAF_DEBUG_MODE=true
GPT_WAF_SYSTEM_ROLE=
GPT_WAF_QUESTION_TEXT=
```
