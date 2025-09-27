# Telegram Quiz Poll Proxy

This is a simple PHP micro-service designed to be deployed on a platform like Render. Its purpose is to act as a proxy for environments (like some free hosting providers) that cannot make direct outbound API calls.

## How it Works

1.  It reads the `TELEGRAM_BOT_TOKEN` and `TELEGRAM_CHAT_ID` from the environment variables set in the Render service.
2.  It receives a `POST` request with JSON data containing the quiz information (question, options, etc.).
3.  It forwards this data to the Telegram Bot API's `sendPoll` endpoint.
4.  It returns the response from the Telegram API to the original caller.

## Setup on Render

1.  Deploy this repository as a "Web Service" on Render.
2.  Go to the "Environment" tab for your service.
3.  Add the following two **Environment Variables**:

| Key | Value |
| --- | --- |
| `TELEGRAM_BOT_TOKEN` | `8031503448:AAHF-aSDI27LJ72rJ_hlU9u97D15uyKx5qg` (Your Bot Token) |
| `TELEGRAM_CHAT_ID` | `-1002712394963` (Your Target Chat ID) |

## API Endpoint

-   **URL:** `/` or `/index.php`
-   **Method:** `POST`
-   **Content-Type:** `application/json`

### Request Body

The request body is now simpler. You only need to send the quiz data.

```json
{
    "question": "Your question text (max 300 chars)",
    "options": ["Option 1", "Option 2", "Option 3", "Option 4"],
    "correct_option_id": 0,
    "explanation": "Your explanation text (max 200 chars)"
}
```
