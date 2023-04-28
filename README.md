# GPT WAF for Laravel

This is a WAF (Web Application Firewall) using GPT-4. (Laravel only)


## Install

```
composer require motikan2010/gpt-waf
```

## Using

### Configuration of ".env"

### Required

- `GPT_WAF_ENABLED` : GPT-WAF enabled or disabled
- `GPT_WAF_OPEN_AI_API_KEY` : OpenAI API key for GPT-4

ex.
```
GPT_WAF_ENABLED=true
GPT_WAF_OPEN_AI_API_KEY=st-tGC**********
```

### Option

- `GPT_WAF_BLOCK_STATUS_CODE` : Status code at block (default: 403)
- `GPT_WAF_DEBUG_MODE` : Output "question" and "answer" to log (default: false)
- `GPT_WAF_QUESTION_TEXT` : Beginning of message when asking a question to GPT
- `GPT_WAF_SYSTEM_ROLE` : GPT role(system). (default: 'You are Security engineer.')
  - By default, the question is asked with the following text.  
  `"Begin your answer with 'Yes' or 'No'.\nIs it possible that the following HTTP request could be related to a cyber attack?\n-----\n"`

ex.
```
GPT_WAF_BLOCK_STATUS_CODE=403
GPT_WAF_DEBUG_MODE=true
GPT_WAF_SYSTEM_ROLE=
GPT_WAF_QUESTION_TEXT=
```
