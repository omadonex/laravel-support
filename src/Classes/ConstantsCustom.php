<?php

namespace Omadonex\LaravelSupport\Classes;

class ConstantsCustom
{
    const REDIRECT_URL = 'redirectUrl';
    const ERROR_MESSAGE = 'errorMessage';
    const MAIN_DATA_GLOBAL = 'mainDataGlobal';
    const MAIN_DATA_GLOBAL_KEY = 'global';
    const MAIN_DATA_PAGE = 'mainDataPage';

    const ACTIVATION_EMAIL_REPEAT_MINUTES = 5;

    const REQUEST_PARAM_ENABLED = '__enabled';
    const REQUEST_PARAM_PAGINATE = '__paginate';
    const REQUEST_PARAM_RELATIONS = '__relations';
    const REQUEST_PARAM_TRASHED = '__trashed';

    const DB_QUERY_TRASHED_WITH = 'with';
    const DB_QUERY_TRASHED_ONLY = 'only';

    const DB_FIELD_TRANS_LANG = 'lang';
    const DB_FIELD_TRANS_MODEL_ID = 'model_id';

    const DB_FIELD_PROTECTED_GENERATE = 'omx_protected_generate';
    const DB_FIELD_UNSAFE_SEEDING = 'omx_unsafe_seeding';

    const DB_FIELD_LEN_LANG = 15;
    const DB_FIELD_LEN_PRIMARY_STR = 36;
    const DB_FIELD_LEN_TOKEN_API = 64;
    const DB_FIELD_LEN_TOKEN_ACTIVATION = 64;

    const TEST_AUTH_TYPE_SESSION = 'session';
    const TEST_AUTH_TYPE_API = 'api';
    const TEST_AUTH_TYPE_GUEST = 'guest';
    const TEST_AUTH_TYPE_NO_MATTER = 'no_matter';
}